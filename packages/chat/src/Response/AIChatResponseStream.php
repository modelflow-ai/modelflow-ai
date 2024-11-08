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

namespace ModelflowAi\Chat\Response;

use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;

readonly class AIChatResponseStream extends AIChatResponse
{
    private AIChatResponseStreamMessageBuilder $messageBuilder;

    /**
     * @param \Iterator<int, AIChatResponseMessage> $messages
     */
    public function __construct(
        private AIChatRequest $request,
        private \Iterator $messages,
        private array $metadata = [],
    ) {
        parent::__construct(
            $request,
            new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, ''),
            null,
            $this->metadata,
        );

        $this->messageBuilder = new AIChatResponseStreamMessageBuilder();
    }

    public function getRequest(): AIChatRequest
    {
        return $this->request;
    }

    public function getMessage(): AIChatResponseMessage
    {
        return $this->messageBuilder->getMessage();
    }

    /**
     * @return \Iterator<int, AIChatResponseMessage>
     */
    public function getMessageStream(): \Iterator
    {
        foreach ($this->messages as $message) {
            $this->messageBuilder->add($message);

            yield $message;
        }
    }
}
