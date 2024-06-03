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

namespace ModelflowAi\Chat\ToolInfo;

use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Response\AIChatToolCall;

interface ToolExecutorInterface
{
    public function execute(AIChatRequest $request, AIChatToolCall $toolCall): AIChatMessage;
}
