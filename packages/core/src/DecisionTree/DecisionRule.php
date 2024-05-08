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

namespace ModelflowAi\Core\DecisionTree;

use ModelflowAi\Core\Behaviour\CriteriaBehaviour;
use ModelflowAi\Core\Behaviour\SupportsBehaviour;
use ModelflowAi\Core\Request\Criteria\AiCriteriaInterface;

/**
 * @template T of CriteriaBehaviour
 * @template U of SupportsBehaviour
 *
 * @implements DecisionRuleInterface<T, U>
 */
class DecisionRule implements DecisionRuleInterface
{
    /**
     * @param U $adapter
     * @param AiCriteriaInterface[] $criteria
     */
    public function __construct(
        private readonly object $adapter,
        private readonly array $criteria = [],
    ) {
    }

    public function matches(object $request): bool
    {
        if (!$request->matches($this->criteria)) {
            return false;
        }

        return $this->adapter->supports($request);
    }

    public function getAdapter(): object
    {
        return $this->adapter;
    }
}
