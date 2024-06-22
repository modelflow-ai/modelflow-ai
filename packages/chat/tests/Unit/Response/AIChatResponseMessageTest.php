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

namespace ModelflowAi\Chat\Tests\Unit\Response;

use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\AIChatToolCall;
use ModelflowAi\Chat\ToolInfo\ToolTypeEnum;
use PHPUnit\Framework\TestCase;

class AIChatResponseMessageTest extends TestCase
{
    public function testRole(): void
    {
        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content');

        $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $message->role);
    }

    public function testContent(): void
    {
        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content');

        $this->assertSame('Test content', $message->content);
    }

    public function testToolCalls(): void
    {
        $toolCalls = [
            new AIChatToolCall(ToolTypeEnum::FUNCTION, '123-123-123', 'name', ['test' => 'test']),
        ];

        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content', $toolCalls);

        $this->assertSame($toolCalls, $message->toolCalls);
    }

    public function testToolCallsNull(): void
    {
        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content', null);

        $this->assertNull($message->toolCalls);
    }
}
