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
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ToolCallPart;
use ModelflowAi\Chat\Response\AIChatToolCall;

class ToolExecutor implements ToolExecutorInterface
{
    public function execute(AIChatRequest $request, AIChatToolCall $toolCall): AIChatMessage
    {
        try {
            $tool = $request->getTools()[$toolCall->name];
            $result = $tool[0]->{$tool[1]}(...$toolCall->arguments);
            if (!\is_string($result)) {
                $result = \json_encode($result, \JSON_THROW_ON_ERROR);
            }
        } catch (\Throwable $exception) {
            $result = $exception->getMessage();
        }

        return new AIChatMessage(AIChatMessageRoleEnum::TOOL, new ToolCallPart($toolCall->id, $toolCall->name, $result));
    }
}
