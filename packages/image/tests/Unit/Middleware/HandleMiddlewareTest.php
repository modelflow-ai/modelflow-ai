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

namespace ModelflowAi\Image\Tests\Unit\Middleware;

use ModelflowAi\Core\DecisionTree\AIModelDecisionTreeInterface;
use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Middleware\HandleMiddleware;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Response\AIImageResponse;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class HandleMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    public function testHandleRequest()
    {
        $response = $this->prophesize(AIImageResponse::class);

        $request = $this->prophesize(AIImageRequest::class);
        $adapter = $this->prophesize(AIImageAdapterInterface::class);
        $adapter->handleRequest($request->reveal())->willReturn($response->reveal());

        $decisionTree = $this->prophesize(AIModelDecisionTreeInterface::class);
        $decisionTree->determineAdapter($request->reveal())->willReturn($adapter->reveal());

        $middleware = new HandleMiddleware($decisionTree->reveal());

        $this->assertSame($response->reveal(), $middleware->handleRequest($request->reveal()));
    }
}
