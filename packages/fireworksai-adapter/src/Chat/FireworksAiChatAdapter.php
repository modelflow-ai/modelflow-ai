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

namespace ModelflowAi\FireworksAiAdapter\Chat;

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ImageBase64Part;
use ModelflowAi\Chat\Request\Message\TextPart;
use ModelflowAi\Chat\Request\Message\ToolCallPart;
use ModelflowAi\Chat\Request\Message\ToolCallsPart;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\AIChatResponseStream;
use ModelflowAi\Chat\Response\AIChatToolCall;
use ModelflowAi\Chat\Response\Usage;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolTypeEnum;
use OpenAI\Contracts\ClientContract;
use OpenAI\Responses\Chat\CreateResponseToolCall;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use OpenAI\Responses\StreamResponse;
use Webmozart\Assert\Assert;

final readonly class FireworksAiChatAdapter implements AIChatAdapterInterface
{
    public function __construct(
        private ClientContract $client,
        private string $model = 'accounts/fireworks/models/llama-v3-70b-instruct',
    ) {
    }

    public function handleRequest(AIChatRequest $request): AIChatResponse
    {
        $format = $request->getFormat();
        Assert::inArray($format, [null, 'json', 'json_schema'], \sprintf('Invalid format "%s" given.', $format));

        $messages = [];

        /** @var AIChatMessage $aiMessage */
        foreach ($request->getMessages() as $aiMessage) {
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
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => \sprintf('data:%s;base64,%s', $part->mimeType, $part->content),
                        ],
                    ];
                } elseif ($part instanceof ToolCallsPart) {
                    $message['tool_calls'] = \array_map(
                        fn (AIChatToolCall $tool) => [
                            'id' => $tool->id,
                            'type' => $tool->type->value,
                            'function' => [
                                'name' => $tool->name,
                                'arguments' => (string) \json_encode($tool->arguments),
                            ],
                        ],
                        $part->toolCalls,
                    );
                } elseif ($part instanceof ToolCallPart) {
                    $message['role'] = 'tool';
                    $message['content'][] = $part->content;
                } else {
                    throw new \Exception('Not supported message part type.');
                }
            }

            if (1 === \count($message['content']) && \is_string($message['content'][0])) {
                $message['content'] = $message['content'][0];
            }

            $messages[] = $message;
        }

        $parameters = [
            'model' => $this->model,
            'messages' => $messages,
        ];

        if (null !== $format) {
            $parameters['response_format'] = ['type' => 'json_object'];
        }

        if ($request->hasTools()) {
            $parameters['tools'] = ToolFormatter::formatTools($request->getToolInfos());
            $toolChoice = $request->getToolChoice();
            if (ToolChoiceEnum::NONE === $toolChoice) {
                unset($parameters['tools']);
            }
        }

        if ($request->getOption('seed')) {
            @\trigger_error('Seed option is not supported by FireworksAi.', \E_USER_WARNING);
        }

        if ($temperature = $request->getOption('temperature')) {
            Assert::float($temperature);
            Assert::range($temperature, 0.0, 2.0, 'Temperature must be between 0 and 2');
            $parameters['temperature'] = $temperature;
        }

        if ($request->isStreamed()) {
            return $this->createStreamed($request, $parameters);
        }

        return $this->create($request, $parameters);
    }

    /**
     * @param array{
     *     model: string,
     *     messages: array<array{
     *         role: "assistant"|"system"|"user"|"tool",
     *         content: string|array<string|array{
     *             type: "text",
     *             text: string,
     *         }|array{
     *             type: "image_url",
     *             image_url: array{
     *                 url: string,
     *             },
     *         }>,
     *         tool_calls?: array<array{
     *             id: string,
     *             type: string,
     *             function: array{
     *                 name: string,
     *                 arguments: string,
     *             },
     *         }>,
     *     }>,
     *     response_format?: array{
     *         type: "json_object",
     *     },
     *     tools?: array<array{
     *         type: string,
     *         function: array{
     *             name: string,
     *             description: string,
     *             parameters: array{
     *                 type: string,
     *                 properties: array<string, mixed>,
     *                 required: array<string>
     *             }
     *         }
     *     }>,
     *     temperature?: float,
     * } $parameters
     */
    protected function create(AIChatRequest $request, array $parameters): AIChatResponse
    {
        $result = $this->client->chat()->create($parameters);

        $choice = $result->choices[0];
        if (0 < \count($choice->message->toolCalls)) {
            return new AIChatResponse(
                $request,
                new AIChatResponseMessage(
                    AIChatMessageRoleEnum::from($choice->message->role),
                    $choice->message->content ?? '',
                    \array_map(
                        fn (CreateResponseToolCall $toolCall) => new AIChatToolCall(
                            ToolTypeEnum::from($toolCall->type),
                            $toolCall->id,
                            $toolCall->function->name,
                            $this->decodeArguments($toolCall->function->arguments),
                        ),
                        $choice->message->toolCalls,
                    ),
                ),
                new Usage(
                    $result->usage->promptTokens,
                    $result->usage->completionTokens ?? 0,
                    $result->usage->totalTokens,
                ),
            );
        }

        return new AIChatResponse(
            $request,
            new AIChatResponseMessage(
                AIChatMessageRoleEnum::from($choice->message->role),
                $choice->message->content ?? '',
            ),
            new Usage(
                $result->usage->promptTokens,
                $result->usage->completionTokens ?? 0,
                $result->usage->totalTokens,
            ),
        );
    }

    /**
     * @param array{
     *     model: string,
     *     messages: array<array{
     *         role: "assistant"|"system"|"user"|"tool",
     *         content: string|array<string|array{
     *             type: "text",
     *             text: string,
     *         }|array{
     *             type: "image_url",
     *             image_url: array{
     *                 url: string,
     *             },
     *         }>,
     *         tool_calls?: array<array{
     *             id: string,
     *             type: string,
     *             function: array{
     *                 name: string,
     *                 arguments: string,
     *             },
     *         }>,
     *     }>,
     *     response_format?: array{
     *         type: "json_object",
     *     },
     *     tools?: array<array{
     *         type: string,
     *         function: array{
     *             name: string,
     *             description: string,
     *             parameters: array{
     *                 type: string,
     *                 properties: array<string, mixed>,
     *                 required: array<string>
     *             }
     *         }
     *     }>,
     *     temperature?: float,
     * } $parameters
     */
    protected function createStreamed(AIChatRequest $request, array $parameters): AIChatResponse
    {
        $responses = $this->client->chat()->createStreamed($parameters);

        return new AIChatResponseStream(
            $request,
            $this->createStreamedMessages($responses),
        );
    }

    /**
     * @param StreamResponse<CreateStreamedResponse> $responses
     *
     * @return \Iterator<int, AIChatResponseMessage>
     */
    protected function createStreamedMessages(StreamResponse $responses): \Iterator
    {
        $role = null;

        /** @var CreateStreamedResponse $response */
        foreach ($responses as $response) {
            $delta = $response->choices[0]->delta;

            if (!$role instanceof AIChatMessageRoleEnum) {
                $role = AIChatMessageRoleEnum::from($delta->role ?? 'assistant');
            }

            if (0 < \count($delta->toolCalls)) {
                foreach ($this->determineToolCall($responses, $response) as $toolCall) {
                    yield new AIChatResponseMessage(
                        $role,
                        $delta->content ?? '',
                        [$toolCall],
                    );
                }

                break;
            }

            if (null !== $delta->content) {
                yield new AIChatResponseMessage(
                    $role,
                    $delta->content,
                );
            }
        }
    }

    /**
     * @param StreamResponse<CreateStreamedResponse> $responses
     *
     * @return \Iterator<int, AIChatToolCall>
     */
    protected function determineToolCall(StreamResponse $responses, CreateStreamedResponse $firstResponse): \Iterator
    {
        $message = [
            'id' => $firstResponse->choices[0]->delta->toolCalls[0]->id,
            'type' => ToolTypeEnum::tryFrom($firstResponse->choices[0]->delta->toolCalls[0]->type ?? '') ?? ToolTypeEnum::FUNCTION,
            'function' => [
                'name' => $firstResponse->choices[0]->delta->toolCalls[0]->function->name,
                'arguments' => [
                    $firstResponse->choices[0]->delta->toolCalls[0]->function->arguments,
                ],
            ],
        ];

        /** @var CreateStreamedResponse $response */
        foreach ($responses as $response) {
            $delta = $response->choices[0]->delta;

            foreach ($delta->toolCalls as $toolCall) {
                if (null !== $toolCall->id) {
                    Assert::inArray($message['type'], ToolTypeEnum::cases());
                    Assert::notNull($message['id']);
                    Assert::isArray($message['function']);
                    Assert::notNull($message['function']['name']);
                    Assert::notNull($message['function']['arguments']);

                    yield new AIChatToolCall(
                        $message['type'],
                        $message['id'],
                        $message['function']['name'],
                        $this->decodeArguments(\implode('', $message['function']['arguments'])),
                    );

                    $message = [
                        'id' => $toolCall->id,
                        'type' => ToolTypeEnum::tryFrom($toolCall->type ?? '') ?? ToolTypeEnum::FUNCTION,
                        'function' => [
                            'name' => $toolCall->function->name,
                            'arguments' => [],
                        ],
                    ];
                }

                $message['function']['arguments'][] = $toolCall->function->arguments;
            }
        }

        Assert::inArray($message['type'], ToolTypeEnum::cases());
        Assert::notNull($message['id']);
        Assert::isArray($message['function']);
        Assert::notNull($message['function']['name']);
        Assert::notNull($message['function']['arguments']);

        yield new AIChatToolCall(
            $message['type'],
            $message['id'],
            $message['function']['name'],
            $this->decodeArguments(\implode('', $message['function']['arguments'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeArguments(string $arguments): array
    {
        /** @var array<string, mixed> $result */
        $result = \json_decode($arguments, true);

        return $result;
    }

    public function supports(object $request): bool
    {
        return $request instanceof AIChatRequest;
    }
}
