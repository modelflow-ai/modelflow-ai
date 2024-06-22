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

namespace ModelflowAi\Chat\Tests\Unit;

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\AIChatRequestHandler;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Builder\AIChatRequestBuilder;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AIChatRequestHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<AIChatAdapterInterface>
     */
    private ObjectProphecy $adapter;

    /**
     * @var ObjectProphecy<DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface>>
     */
    private ObjectProphecy $decisionTree;

    private AIChatRequestHandler $aiRequestHandler;

    protected function setUp(): void
    {
        $this->adapter = $this->prophesize(AIChatAdapterInterface::class);
        /** @var ObjectProphecy<DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface>> $decisionTree */
        $decisionTree = $this->prophesize(DecisionTreeInterface::class);
        $this->decisionTree = $decisionTree;
        $this->aiRequestHandler = new AIChatRequestHandler($this->decisionTree->reveal());
    }

    public function testCreateRequest(): void
    {
        $chatRequest = $this->aiRequestHandler->createRequest();

        $this->assertInstanceOf(AIChatRequestBuilder::class, $chatRequest);
    }

    public function testHandleChatRequest(): void
    {
        $chatRequest = $this->aiRequestHandler->createRequest(
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'Test content'),
        )->build();

        $response = new AIChatResponse(
            $chatRequest,
            new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Response content'),
        );
        $this->adapter->handleRequest(Argument::type(AIChatRequest::class))->willReturn($response);
        $this->decisionTree->determineAdapter($chatRequest)->willReturn($this->adapter->reveal());

        $result = $chatRequest->execute();

        $this->assertSame($response, $result);
    }
}
