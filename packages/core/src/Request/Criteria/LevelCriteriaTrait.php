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

namespace ModelflowAi\Core\Request\Criteria;

use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\DecisionEnum;

trait LevelCriteriaTrait
{
    public function matches(CriteriaInterface $toMatch): DecisionEnum
    {
        if (!$toMatch instanceof self) {
            return DecisionEnum::ABSTAIN;
        }

        return $this->value <= $toMatch->value ? DecisionEnum::MATCH : DecisionEnum::NO_MATCH;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
