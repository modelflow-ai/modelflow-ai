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

use ModelflowAi\Chat\Request\ResponseFormat\ResponseFormatInterface;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;

interface ExpertInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getInstructions(): string;

    /**
     * @return CriteriaInterface[]
     */
    public function getCriteria(): array;

    public function getResponseFormat(): ?ResponseFormatInterface;
}
