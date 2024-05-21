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

namespace ModelflowAi\DecisionTree;

use ModelflowAi\DecisionTree\Behaviour\CriteriaBehaviour;
use ModelflowAi\DecisionTree\Behaviour\SupportsBehaviour;

/**
 * @template T of CriteriaBehaviour
 * @template U of SupportsBehaviour
 *
 * @implements DecisionTreeInterface<T, U>
 */
final readonly class DecisionTree implements DecisionTreeInterface
{
    /**
     * @param DecisionRuleInterface<T, U>[] $rules
     */
    public function __construct(private array $rules)
    {
    }

    public function determineAdapter(object $request): object
    {
        foreach ($this->rules as $rule) {
            if ($rule->matches($request)) {
                return $rule->getAdapter();
            }
        }

        throw new \Exception('No matching adapter found.');
    }
}
