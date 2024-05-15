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

namespace ModelflowAi\Image\Tests\Unit\Response;

use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Response\AIImageResponse;
use ModelflowAi\Image\Tests\ResourceTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AIImageResponseTest extends TestCase
{
    use ProphecyTrait;
    use ResourceTrait;

    public function testConstruct(): void
    {
        $request = $this->prophesize(AIImageRequest::class)->reveal();
        $response = new AIImageResponse(
            $request,
            ImageFormat::JPEG,
            'IMAGE',
        );

        $this->assertSame($request, $response->request);
        $this->assertSame(ImageFormat::JPEG, $response->imageFormat);
        $this->assertSame('IMAGE', $response->resource);
    }

    public function testStream(): void
    {
        $stream = $this->getDogImageResource();

        $request = $this->prophesize(AIImageRequest::class)->reveal();
        $response = new AIImageResponse(
            $request,
            ImageFormat::JPEG,
            $stream,
        );

        $this->assertSame(
            $stream,
            $response->stream(),
        );
    }

    public function testStreamWithConversion(): void
    {
        $base64 = $this->getDogImageBase64();

        $request = $this->prophesize(AIImageRequest::class)->reveal();
        $response = new AIImageResponse(
            $request,
            ImageFormat::JPEG,
            $base64,
        );

        $this->assertSame(
            \stream_get_contents($this->getDogImageResource()),
            \stream_get_contents($response->stream()),
        );
    }

    public function testBase64(): void
    {
        $base64 = $this->getDogImageBase64();

        $request = $this->prophesize(AIImageRequest::class)->reveal();
        $response = new AIImageResponse(
            $request,
            ImageFormat::JPEG,
            $base64,
        );

        $this->assertSame(
            $base64,
            $response->base64(),
        );
    }

    public function testBase64WithConversion(): void
    {
        $stream = $this->getDogImageResource();

        $request = $this->prophesize(AIImageRequest::class)->reveal();
        $response = new AIImageResponse(
            $request,
            ImageFormat::JPEG,
            $stream,
        );

        $this->assertSame(
            $this->getDogImageBase64(),
            $response->base64(),
        );
    }
}
