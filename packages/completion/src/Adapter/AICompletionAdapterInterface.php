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

namespace ModelflowAi\Completion\Adapter;

use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\Completion\Response\AICompletionResponse;
use ModelflowAi\DecisionTree\Behaviour\SupportsBehaviour;

interface AICompletionAdapterInterface extends SupportsBehaviour
{
    public function handleRequest(AICompletionRequest $request): AICompletionResponse;
}
