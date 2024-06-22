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

namespace ModelflowAi\Completion\Request\Builder;

use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;

class AICompletionRequestBuilder
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
     * }
     */
    protected array $options = [];

    /**
     * @var callable
     */
    protected $requestHandler;

    private ?string $prompt = null;

    public function __construct(
        callable $requestHandler,
    ) {
        $this->requestHandler = $requestHandler;

        $this->criteria = new CriteriaCollection();
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

    public function prompt(?string $prompt = null): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function build(): AICompletionRequest
    {
        if (null === $this->prompt) {
            throw new \RuntimeException('No text given');
        }

        return new AICompletionRequest(
            $this->prompt,
            $this->criteria,
            $this->options,
            $this->requestHandler,
        );
    }
}
