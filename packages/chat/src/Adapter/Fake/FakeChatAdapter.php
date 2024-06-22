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

namespace ModelflowAi\Chat\Adapter\Fake;

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\AIChatResponseStream;
use Webmozart\Assert\Assert;

class FakeChatAdapter implements AIChatAdapterInterface
{
    /**
     * @var array<AIChatResponseMessage|AIChatResponseMessage[]>
     */
    private array $messages = [];

    /**
     * @param AIChatResponseMessage|AIChatResponseMessage[] $message
     */
    public function addMessage(AIChatResponseMessage|array $message): void
    {
        $this->messages[] = $message;
    }

    public function handleRequest(AIChatRequest $request): AIChatResponse
    {
        /** @var AIChatResponseMessage|AIChatResponseMessage[] $message */
        $message = \array_shift($this->messages);
        Assert::notNull($message);

        if ($request->getOption('streamed', false)) {
            if (!\is_array($message)) {
                $message = [$message];
            }

            return new AIChatResponseStream($request, $this->stream($message));
        }

        Assert::isInstanceOf($message, AIChatResponseMessage::class);

        return new AIChatResponse($request, $message);
    }

    public function supports(object $request): bool
    {
        return $request instanceof AIChatRequest;
    }

    /**
     * @param AIChatResponseMessage[] $messages
     *
     * @return \Generator<int, AIChatResponseMessage>
     */
    public function stream(array $messages): \Generator
    {
        foreach ($messages as $message) {
            yield $message;

            \usleep(500000);
        }
    }
}
