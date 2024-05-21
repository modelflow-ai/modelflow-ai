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
 */
interface DecisionTreeInterface
{
    /**
     * @param T $request
     *
     * @return U
     */
    public function determineAdapter(object $request): object;
}
