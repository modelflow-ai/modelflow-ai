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

namespace ModelflowAi\AnthropicAdapter\Chat;

use ModelflowAi\Anthropic\ClientInterface;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\Anthropic\Resources\MessagesInterface;
use ModelflowAi\Anthropic\Responses\Messages\CreateStreamedResponse;
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ImageBase64Part;
use ModelflowAi\Chat\Request\Message\TextPart;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\AIChatResponseStream;

/**
 * @phpstan-import-type Parameters from MessagesInterface
 */
final readonly class AnthropicChatAdapter implements AIChatAdapterInterface
{
    public const EXPECTED_ROLES = [
        AIChatMessageRoleEnum::SYSTEM,
        AIChatMessageRoleEnum::ASSISTANT,
        AIChatMessageRoleEnum::USER,
    ];

    public function __construct(
        private ClientInterface $client,
        private Model $model,
        private int $maxTokens = 1024,
    ) {
    }

    public function handleRequest(AIChatRequest $request): AIChatResponse
    {
        /** @var Parameters $parameters */
        $parameters = [
            'model' => $this->model->value,
            'messages' => [],
            'max_tokens' => $this->maxTokens,
        ];

        $messages = [];
        /** @var AIChatMessage $aiMessage */
        foreach ($request->getMessages() as $aiMessage) {
            if (!\in_array($aiMessage->role, self::EXPECTED_ROLES, true)) {
                throw new \Exception('Not supported message role.');
            }

            $message = [
                'role' => $aiMessage->role->value,
                'content' => [],
            ];

            foreach ($aiMessage->parts as $part) {
                if ($part instanceof TextPart) {
                    $message['content'][] = [
                        'type' => 'text',
                        'text' => $part->text,
                    ];
                } elseif ($part instanceof ImageBase64Part) {
                    $message['content'][] = [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $part->mimeType,
                            'data' => $part->content,
                        ],
                    ];
                } else {
                    throw new \Exception('Not supported message part type.');
                }
            }

            if (1 === \count($message['content']) && 'text' === $message['content'][0]['type']) {
                $message['content'] = $message['content'][0]['text'];
            }

            $messages[] = $message;
        }

        $parameters['messages'] = $messages;

        if ($request->getOption('streamed', false)) {
            return $this->createStreamed($request, $parameters);
        }

        return $this->create($request, $parameters);
    }

    /**
     * @param Parameters $parameters
     */
    protected function create(AIChatRequest $request, array $parameters): AIChatResponse
    {
        $result = $this->client->messages()->create($parameters);

        return new AIChatResponse(
            $request,
            new AIChatResponseMessage(
                AIChatMessageRoleEnum::from($result->role),
                $result->content[0]->text ?? '',
            ),
        );
    }

    /**
     * @param Parameters $parameters
     */
    protected function createStreamed(AIChatRequest $request, array $parameters): AIChatResponse
    {
        $responses = $this->client->messages()->createStreamed($parameters);

        return new AIChatResponseStream(
            $request,
            $this->createStreamedMessages($responses),
        );
    }

    /**
     * @param \Iterator<int, CreateStreamedResponse> $responses
     *
     * @return \Iterator<int, AIChatResponseMessage>
     */
    protected function createStreamedMessages(\Iterator $responses): \Iterator
    {
        $role = null;

        foreach ($responses as $response) {
            $delta = $response->content;

            if (!$role instanceof AIChatMessageRoleEnum) {
                $role = AIChatMessageRoleEnum::from($response->role ?? 'assistant');
            }

            $text = $delta->text ?? '';
            if ('' === $text) {
                continue;
            }

            yield new AIChatResponseMessage($role, $text);
        }
    }

    public function supports(object $request): bool
    {
        return $request instanceof AIChatRequest
            && !$request->hasTools();
    }
}
