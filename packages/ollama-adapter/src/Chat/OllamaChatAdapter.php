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

namespace ModelflowAi\OllamaAdapter\Chat;

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\AIChatResponseStream;
use ModelflowAi\Ollama\ClientInterface;
use ModelflowAi\Ollama\Responses\Chat\CreateStreamedResponse;
use Webmozart\Assert\Assert;

final readonly class OllamaChatAdapter implements AIChatAdapterInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $model = 'llama2',
    ) {
    }

    public function handleRequest(AIChatRequest $request): AIChatResponse
    {
        /** @var "json"|null $format */
        $format = $request->getOption('format');
        Assert::inArray($format, [null, 'json'], \sprintf('Invalid format "%s" given.', $format));

        $attributes = [
            'model' => $this->model,
            'messages' => $request->getMessages()->toArray(),
        ];

        if ($format) {
            $attributes['format'] = $format;
        }

        if ($request->getOption('streamed', false)) {
            return $this->createStreamed($request, $attributes);
        }

        return $this->create($request, $attributes);
    }

    /**
     * @param array{
     *     model: string,
     *     messages: array<array{
     *         role: "assistant"|"system"|"user"|"tool",
     *         content: string,
     *     }>,
     *     format?: "json",
     * } $parameters
     */
    protected function create(AIChatRequest $request, array $parameters): AIChatResponse
    {
        $response = $this->client->chat()->create($parameters);

        return new AIChatResponse(
            $request,
            new AIChatResponseMessage(
                AIChatMessageRoleEnum::from($response->message->role),
                $response->message->content ?? '',
            ),
        );
    }

    /**
     * @param array{
     *     model: string,
     *     messages: array<array{
     *         role: "assistant"|"system"|"user"|"tool",
     *         content: string,
     *     }>,
     *     format?: "json",
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
     * @param \Iterator<int, CreateStreamedResponse> $responses
     *
     * @return \Iterator<int, AIChatResponseMessage>
     */
    protected function createStreamedMessages(\Iterator $responses): \Iterator
    {
        $role = null;

        foreach ($responses as $response) {
            if (!$role instanceof AIChatMessageRoleEnum) {
                $role = AIChatMessageRoleEnum::from($response->message->role);
            }

            yield new AIChatResponseMessage(
                $role,
                $response->message->delta ?? '',
            );
        }
    }

    public function supports(object $request): bool
    {
        return $request instanceof AIChatRequest;
    }
}
