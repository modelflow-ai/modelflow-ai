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

use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\Experts\ResponseFormat\ResponseFormatInterface;

readonly class Expert implements ExpertInterface
{
    /**
     * @param CriteriaInterface[] $criteria
     */
    public function __construct(
        private string $name,
        private string $description,
        private string $instructions,
        private array $criteria,
        private ?ResponseFormatInterface $responseFormat = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getInstructions(): string
    {
        return $this->instructions;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function getResponseFormat(): ?ResponseFormatInterface
    {
        return $this->responseFormat;
    }
}
