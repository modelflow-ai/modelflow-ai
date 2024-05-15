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

namespace ModelflowAi\Image\Tests\Unit;

use ModelflowAi\Image\AIImageRequestHandler;
use ModelflowAi\Image\Middleware\MiddlewareInterface;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Response\AIImageResponse;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AIImageRequestHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testHandle(): void
    {
        $response = new AIImageResponse($this->prophesize(AIImageRequest::class)->reveal(), ImageFormat::JPEG, 'IMAGE');

        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->handleRequest(Argument::type(AIImageRequest::class))->willReturn($response);

        $handler = new AIImageRequestHandler($middleware->reveal());

        $result = $handler->createRequest()
            ->textToImage('cute cat')
            ->imageFormat(ImageFormat::JPEG)
            ->build()
            ->execute();

        $this->assertSame($response, $result);
    }

    public function testHandleWrongFormat(): void
    {
        $this->expectException(\RuntimeException::class);

        $response = new AIImageResponse($this->prophesize(AIImageRequest::class)->reveal(), ImageFormat::PNG, 'IMAGE');

        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->handleRequest(Argument::type(AIImageRequest::class))->willReturn($response);

        $handler = new AIImageRequestHandler($middleware->reveal());

        $handler->createRequest()
            ->textToImage('cute cat')
            ->imageFormat(ImageFormat::JPEG)
            ->build()
            ->execute();
    }
}
