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

use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\Usage;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AIChatResponseTest extends TestCase
{
    use ProphecyTrait;

    public function testGetMessage(): void
    {
        $request = $this->prophesize(AIChatRequest::class);

        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content');
        $response = new AIChatResponse($request->reveal(), $message, new Usage(0, 0, 0));

        $this->assertSame($message, $response->getMessage());
    }

    public function testGetUsage(): void
    {
        $request = $this->prophesize(AIChatRequest::class);

        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content');
        $usage = new Usage(0, 0, 0);
        $response = new AIChatResponse($request->reveal(), $message, $usage);

        $this->assertSame($usage, $response->getUsage());
    }

    public function testGetMetadata(): void
    {
        $request = $this->prophesize(AIChatRequest::class);

        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content');
        $response = new AIChatResponse($request->reveal(), $message, new Usage(0, 0, 0), ['test' => 'value']);

        $this->assertSame(['test' => 'value'], $response->getMetadata());
    }

    public function testGetRequest(): void
    {
        $request = $this->prophesize(AIChatRequest::class);

        $message = new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content');
        $response = new AIChatResponse($request->reveal(), $message, new Usage(0, 0, 0));

        $this->assertSame($request->reveal(), $response->getRequest());
    }
}
