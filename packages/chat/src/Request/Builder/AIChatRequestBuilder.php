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

namespace ModelflowAi\Chat\Request\Builder;

use ModelflowAi\Chat\Request\AIChatMessageCollection;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\MessagePart;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolInfoBuilder;
use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;

final class AIChatRequestBuilder
{
    public static function create(callable $requestHandler): self
    {
        return new self($requestHandler);
    }

    protected CriteriaCollection $criteria;

    /**
     * @var array{
     *     format?: "json"|null,
     *     streamed?: bool,
     *     toolChoice?: ToolChoiceEnum,
     * }
     */
    protected array $options = [];

    /**
     * @var callable
     */
    protected $requestHandler;

    /**
     * @var AIChatMessage[]
     */
    protected array $messages = [];

    /**
     * @var array<string, array{0: object, 1: string}>
     */
    protected array $tools = [];

    public function __construct(
        callable $requestHandler,
    ) {
        $this->requestHandler = $requestHandler;

        $this->criteria = new CriteriaCollection();
    }

    /**
     * @param array{
     *      format?: "json"|null,
     *      streamed?: bool,
     *      toolChoice?: ToolChoiceEnum,
     *      seed?: int,
     *      temperature?: float,
     *  } $options
     */
    public function addOptions(array $options): self
    {
        $this->options = \array_merge($this->options, $options);

        return $this;
    }

    public function asJson(): self
    {
        $this->options['format'] = 'json';

        return $this;
    }

    public function streamed(): self
    {
        $this->options['streamed'] = true;

        return $this;
    }

    /**
     * @param CriteriaInterface|CriteriaInterface[] $criteria
     */
    public function addCriteria(CriteriaInterface|array $criteria): self
    {
        $criteria = \is_array($criteria) ? $criteria : [$criteria];

        $this->criteria = new CriteriaCollection(
            \array_merge($this->criteria->all, $criteria),
        );

        return $this;
    }

    public function addMessage(AIChatMessage $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @param AIChatMessage[] $messages
     */
    public function addMessages(array $messages): self
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }

        return $this;
    }

    /**
     * @param MessagePart[]|MessagePart|string $content
     */
    public function addSystemMessage(array|MessagePart|string $content): self
    {
        $this->messages[] = new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, $content);

        return $this;
    }

    /**
     * @param MessagePart[]|MessagePart|string $content
     */
    public function addAssistantMessage(array|MessagePart|string $content): self
    {
        $this->messages[] = new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, $content);

        return $this;
    }

    /**
     * @param MessagePart[]|MessagePart|string $content
     */
    public function addUserMessage(array|MessagePart|string $content): self
    {
        $this->messages[] = new AIChatMessage(AIChatMessageRoleEnum::USER, $content);

        return $this;
    }

    public function toolChoice(ToolChoiceEnum $toolChoice): self
    {
        $this->options['toolChoice'] = $toolChoice;

        return $this;
    }

    public function tool(string $name, object $instance, ?string $method = null): self
    {
        $this->tools[$name] = [$instance, $method ?? $name];

        return $this;
    }

    public function build(): AIChatRequest
    {
        $toolInfos = \array_map(
            fn (string $name, array $tool) => ToolInfoBuilder::buildToolInfo($tool[0], $tool[1], $name),
            \array_keys($this->tools),
            $this->tools,
        );

        return new AIChatRequest(
            new AIChatMessageCollection(...$this->messages),
            $this->criteria,
            $this->tools,
            $toolInfos,
            $this->options,
            $this->requestHandler,
        );
    }
}
