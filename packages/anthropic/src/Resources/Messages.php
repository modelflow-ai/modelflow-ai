<?php

declare(strict_types=1);

/*
 * This file is part of the Modelflow AI package.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ModelflowAi\Anthropic\Resources;

use ModelflowAi\Anthropic\Responses\Messages\CreateResponse;
use ModelflowAi\Anthropic\Responses\Messages\CreateStreamedResponse;
use ModelflowAi\ApiClient\Resources\Concerns\Streamable;
use ModelflowAi\ApiClient\Transport\Payload;
use ModelflowAi\ApiClient\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-import-type Message from MessagesInterface
 */
final readonly class Messages implements MessagesInterface
{
    use Streamable;

    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    public function create(array $parameters): CreateResponse
    {
        $this->ensureNotStreamed($parameters);
        $this->validateParameters($parameters);

        $parameters = $this->extractSystemPrompts($parameters);

        $payload = Payload::create('messages', $parameters);

        $response = $this->transport->requestObject($payload);

        // @phpstan-ignore-next-line
        return CreateResponse::from($response->data, $response->meta);
    }

    public function createStreamed(array $parameters): \Iterator
    {
        $this->validateParameters($parameters);
        $parameters['stream'] = true;

        $parameters = $this->extractSystemPrompts($parameters);

        $payload = Payload::create('messages', $parameters);

        /**
         * @see https://docs.anthropic.com/claude/reference/messages-streaming#raw-http-stream-response
         */
        $decoder = function (ChunkInterface $chunk) use (&$message): \Iterator {
            $content = $chunk->getContent();

            $lines = \array_filter(\explode(\PHP_EOL, $content));
            $events = \array_chunk($lines, 2);
            foreach ($events as $event) {
                if (2 !== \count($event)) {
                    continue;
                }

                $eventName = \trim(\substr($event[0], 6));
                $data = \trim(\substr($event[1], 5));

                if ('ping' === $eventName) {
                    continue;
                } elseif ('message_start' === $eventName) {
                    /** @var array{
                     *     message: array{
                     *          id: string,
                     *          type: "message",
                     *          role: "assistant",
                     *          content: array{},
                     *          model: string,
                     *          stop_reason: null,
                     *          stop_sequence: null,
                     *          usage: array{input_tokens: int, output_tokens: int},
                     *     },
                     * } $object */
                    $object = \json_decode($data, true);
                    $message = $object['message'];

                    yield $message;
                } elseif ('content_block_start' === $eventName) {
                    /** @var array{
                     *     index: int,
                     *     content_block: array{
                     *         type: "text",
                     *         text: string,
                     *     },
                     * } $object
                     */
                    $object = \json_decode($data, true);
                    Assert::isArray($object['content_block']);

                    // @phpstan-ignore-next-line
                    yield [...$message, 'content' => ['index' => $object['index'], ...$object['content_block']]];
                } elseif ('content_block_delta' === $eventName) {
                    /** @var array{
                     *     index: int,
                     *     delta: array{
                     *         type: "text_delta",
                     *         text: string,
                     *     },
                     * } $object
                     */
                    $object = \json_decode($data, true);

                    // @phpstan-ignore-next-line
                    yield [...$message, 'content' => ['index' => $object['index'], ...$object['delta']]];
                } elseif ('content_block_stop' === $eventName) {
                    continue;
                } elseif ('message_delta' === $eventName) {
                    /** @var array{
                     *     delta: array{
                     *         stop_reason: string,
                     *         stop_sequence: string|null,
                     *     },
                     *          usage: array{
                     *              input_tokens?: int,
                     *              output_tokens?: int,
                     *          },
                     * } $object
                     */
                    $object = \json_decode($data, true);

                    $message['usage'] = [
                        'input_tokens' => ($message['usage']['input_tokens'] ?? 0) + ($object['usage']['input_tokens'] ?? 0),
                        'output_tokens' => ($message['usage']['output_tokens'] ?? 0) + ($object['usage']['output_tokens'] ?? 0),
                    ];

                    $message = [...$message, ...$object['delta']];

                    yield $message;
                } elseif ('message_stop' === $eventName) {
                    continue;
                }
            }
        };

        foreach ($this->transport->requestStream($payload, $decoder) as $index => $response) {
            // @phpstan-ignore-next-line
            yield CreateStreamedResponse::from($index, $response->data, $response->meta);
        }
    }

    /**
     * @param array{
     *       model: string,
     *       messages: Message[],
     *       max_tokens: int,
     *       metadata?: array{user_id: string},
     *       stop_sequences?: string[],
     *       temperature?: float,
     *       top_k?: int,
     *       top_p?: float,
     *  } $parameters
     *
     * @return array{
     *        model: string,
     *        messages: Message[],
     *        max_tokens: int,
     *        metadata?: array{user_id: string},
     *        stop_sequences?: string[],
     *        temperature?: float,
     *        top_k?: int,
     *        top_p?: float,
     *   }
     */
    private function extractSystemPrompts(array $parameters): array
    {
        $messages = [];
        $systemPrompts = [];
        foreach ($parameters['messages'] as $message) {
            if ('system' === $message['role']) {
                $content = $message['content'];
                if (\is_array($content)) {
                    if ('image' === $content['type']) {
                        throw new \InvalidArgumentException('Invalid message content type for a system message.');
                    }
                    $content = $content['text'];
                }

                $systemPrompts[] = $content;

                continue;
            }

            $messages[] = $message;
        }

        $parameters['messages'] = $messages;
        $parameters['system'] = \implode(\PHP_EOL, \array_filter($systemPrompts));

        return $parameters;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function validateParameters(array $parameters): void
    {
        Assert::keyExists($parameters, 'model');
        Assert::string($parameters['model']);

        Assert::keyExists($parameters, 'messages');
        Assert::isArray($parameters['messages']);
        foreach ($parameters['messages'] as $message) {
            Assert::keyExists($message, 'role');
            Assert::string($message['role']);
            Assert::inArray($message['role'], ['system', 'user', 'assistant', 'tool']);
            Assert::keyExists($message, 'content');

            if (\is_string($message['content'])) {
                continue;
            }

            Assert::isArray($message['content']);
            Assert::keyExists($message['content'], 'type');
            Assert::string($message['content']['type']);

            if ('text' === $message['content']['type']) {
                Assert::keyExists($message['content'], 'text');
                Assert::string($message['content']['text']);
            } elseif ('image' === $message['content']['type']) {
                Assert::keyExists($message['content'], 'source');
                Assert::isArray($message['content']['source']);
                Assert::keyExists($message['content']['source'], 'type');
                Assert::string($message['content']['source']['type']);
                Assert::same($message['content']['source']['type'], 'base64');
                Assert::keyExists($message['content']['source'], 'media_type');
                Assert::string($message['content']['source']['media_type']);
                Assert::keyExists($message['content']['source'], 'data');
                Assert::string($message['content']['source']['data']);
            } else {
                throw new \InvalidArgumentException('Invalid message content type');
            }
        }

        Assert::keyExists($parameters, 'max_tokens');
        Assert::integer($parameters['max_tokens']);

        if (isset($parameters['metadata'])) {
            Assert::isArray($parameters['metadata']);
            Assert::keyExists($parameters['metadata'], 'user_id');
            Assert::string($parameters['metadata']['user_id']);
        }

        if (isset($parameters['stop_sequences'])) {
            Assert::isArray($parameters['stop_sequences']);
            foreach ($parameters['stop_sequences'] as $sequence) {
                Assert::string($sequence);
            }
        }

        if (isset($parameters['temperature'])) {
            Assert::float($parameters['temperature']);
        }

        if (isset($parameters['top_k'])) {
            Assert::integer($parameters['top_k']);
        }

        if (isset($parameters['top_p'])) {
            Assert::float($parameters['top_p']);
        }
    }
}
