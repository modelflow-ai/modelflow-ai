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

namespace ModelflowAi\Completion\Tests\Unit;

use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Completion\AICompletionRequestHandler;
use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\Completion\Request\Builder\AICompletionRequestBuilder;
use ModelflowAi\Completion\Response\AICompletionResponse;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AICompletionRequestHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<AICompletionAdapterInterface>
     */
    private ObjectProphecy $adapter;

    /**
     * @var ObjectProphecy<DecisionTreeInterface<AICompletionRequest, AICompletionAdapterInterface>>
     */
    private ObjectProphecy $decisionTree;

    private AICompletionRequestHandler $aiRequestHandler;

    protected function setUp(): void
    {
        $this->adapter = $this->prophesize(AICompletionAdapterInterface::class);
        /** @var ObjectProphecy<DecisionTreeInterface<AICompletionRequest, AICompletionAdapterInterface>> $decisionTree */
        $decisionTree = $this->prophesize(DecisionTreeInterface::class);
        $this->decisionTree = $decisionTree;
        $this->aiRequestHandler = new AICompletionRequestHandler($this->decisionTree->reveal());
    }

    public function testCreateRequest(): void
    {
        $textRequest = $this->aiRequestHandler->createRequest('Test content');

        $this->assertInstanceOf(AICompletionRequestBuilder::class, $textRequest);
    }

    public function testHandleRequest(): void
    {
        $textRequest = $this->aiRequestHandler->createRequest('Test content')->build();

        $response = new AICompletionResponse($textRequest, 'Response content');
        $this->adapter->handleRequest(Argument::type(AICompletionRequest::class))->willReturn($response);
        $this->decisionTree->determineAdapter($textRequest)->willReturn($this->adapter->reveal());

        $result = $textRequest->execute();

        $this->assertSame($response, $result);
    }
}
