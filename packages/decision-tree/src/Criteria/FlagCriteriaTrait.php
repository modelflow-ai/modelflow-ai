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

namespace ModelflowAi\DecisionTree\Criteria;

use ModelflowAi\DecisionTree\DecisionEnum;

trait FlagCriteriaTrait
{
    public function matches(CriteriaInterface $toMatch): DecisionEnum
    {
        if (!$toMatch instanceof self) {
            return DecisionEnum::ABSTAIN;
        }

        return $this->getValue() === $toMatch->getValue() ? DecisionEnum::MATCH : DecisionEnum::SAME_TYPE;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
