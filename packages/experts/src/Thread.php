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

namespace ModelflowAi\Experts;

use ModelflowAi\Chat\AIChatRequestHandlerInterface;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\MessagePart;
use ModelflowAi\Chat\Request\ResponseFormat\ResponseFormatInterface;
use ModelflowAi\Chat\Response\AIChatResponse;

class Thread implements ThreadInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @var AIChatMessage[]
     */
    private array $messages = [];

    /**
     * @var array<string, mixed>
     */
    private array $metadata = [];

    public function __construct(
        private readonly AIChatRequestHandlerInterface $requestHandler,
        private readonly ExpertInterface $expert,
    ) {
    }

    public function addContext(string $key, mixed $data): self
    {
        $this->context[$key] = $data;

        return $this;
    }

    public function addMetadata(array $metadata): self
    {
        $this->metadata = \array_merge($this->metadata, $metadata);

        return $this;
    }

    public function addMessage(AIChatMessage $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    public function addSystemMessage(array|MessagePart|string $content): self
    {
        return $this->addMessage(new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, $content));
    }

    public function addAssistantMessage(array|MessagePart|string $content): self
    {
        return $this->addMessage(new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, $content));
    }

    public function addUserMessage(array|MessagePart|string $content): self
    {
        return $this->addMessage(new AIChatMessage(AIChatMessageRoleEnum::USER, $content));
    }

    /**
     * @param AIChatMessage[] $messages
     */
    public function addMessages(array $messages): self
    {
        $this->messages = \array_merge($this->messages, $messages);

        return $this;
    }

    public function run(): AIChatResponse
    {
        $builder = $this->requestHandler->createRequest()
            ->addSystemMessage($this->expert->getInstructions())
            ->addCriteria($this->expert->getCriteria());

        if ($this->expert->getResponseFormat() instanceof ResponseFormatInterface) {
            $builder->asJson($this->expert->getResponseFormat());
        }

        if ([] !== $this->context) {
            $builder->addUserMessage('Context: ' . \json_encode($this->context));
        }
        if ([] !== $this->metadata) {
            $builder->addMetadata($this->metadata);
        }

        foreach ($this->messages as $message) {
            $builder->addMessage($message);
        }

        return $builder->build()
            ->execute();
    }
}
