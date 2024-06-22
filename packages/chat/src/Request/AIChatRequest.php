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

namespace ModelflowAi\Chat\Request;

use ModelflowAi\Chat\Request\Message\ImageBase64Part;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolInfo;
use ModelflowAi\DecisionTree\Behaviour\CriteriaBehaviour;
use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\DecisionTree\Criteria\FeatureCriteria;

class AIChatRequest implements CriteriaBehaviour
{
    private readonly CriteriaCollection $criteria;

    /**
     * @var callable
     */
    protected $requestHandler;

    /**
     * @param array<string, array{0: object, 1: string}> $tools
     * @param ToolInfo[] $toolInfos
     * @param array{
     *     streamed?: boolean,
     *     format?: "json"|null,
     *     toolChoice?: ToolChoiceEnum,
     * } $options
     */
    public function __construct(
        private readonly AIChatMessageCollection $messages,
        CriteriaCollection $criteria,
        private readonly array $tools,
        private readonly array $toolInfos,
        private array $options,
        callable $requestHandler,
    ) {
        $features = [];

        $latest = $this->messages->latest();
        foreach ($latest?->parts ?? [] as $part) {
            if ($part instanceof ImageBase64Part) {
                $features[] = FeatureCriteria::IMAGE_TO_TEXT;
            }
        }

        if ($this->getOption('streamed', false)) {
            $features[] = FeatureCriteria::STREAM;
        }

        if ([] !== $this->tools && ToolChoiceEnum::AUTO === $this->getOption('toolChoice', ToolChoiceEnum::AUTO)) {
            $features[] = FeatureCriteria::TOOLS;
        }

        $this->criteria = $criteria->withFeatures($features);
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param "format"|"streamed"|"toolChoice" $key
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function matches(array $criteria): bool
    {
        return $this->criteria->matches($criteria);
    }

    public function getMessages(): AIChatMessageCollection
    {
        return $this->messages;
    }

    public function hasTools(): bool
    {
        return [] !== $this->tools;
    }

    /**
     * @return array<string, array{0: object, 1: string}>
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * @return ToolInfo[]
     */
    public function getToolInfos(): array
    {
        return $this->toolInfos;
    }

    public function execute(): AIChatResponse
    {
        return \call_user_func($this->requestHandler, $this);
    }
}
