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

namespace ModelflowAi\Image\Tests\Unit\Request;

use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\Image\Request\Action\TextToImageAction;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Request\Value\OutputFormat;
use ModelflowAi\Image\Response\AIImageResponse;
use PHPUnit\Framework\TestCase;

class AIImageRequestTest extends TestCase
{
    public function testConstruct(): void
    {
        $handler = function (AIImageRequest $request): AIImageResponse {
            $this->assertInstanceOf(TextToImageAction::class, $request->action);
            $this->assertSame('cute cat', $request->action->prompt);

            return new AIImageResponse($request, ImageFormat::JPEG, 'IMAGE');
        };

        $request = new AIImageRequest(
            new TextToImageAction('cute cat'),
            ImageFormat::JPEG,
            OutputFormat::BASE64,
            new CriteriaCollection([CapabilityCriteria::BASIC]),
            $handler,
        );

        $this->assertInstanceOf(TextToImageAction::class, $request->action);
        $this->assertSame('cute cat', $request->action->prompt);
        $this->assertSame(ImageFormat::JPEG, $request->imageFormat);
        $this->assertSame(OutputFormat::BASE64, $request->outputFormat);
        $this->assertSame([CapabilityCriteria::BASIC], $request->criteriaCollection->all);
    }

    public function testMatches(): void
    {
        $handler = function (AIImageRequest $request): AIImageResponse {
            $this->assertInstanceOf(TextToImageAction::class, $request->action);
            $this->assertSame('cute cat', $request->action->prompt);

            return new AIImageResponse($request, ImageFormat::JPEG, 'IMAGE');
        };

        $request = new AIImageRequest(
            new TextToImageAction('cute cat'),
            ImageFormat::JPEG,
            OutputFormat::BASE64,
            new CriteriaCollection([CapabilityCriteria::ADVANCED]),
            $handler,
        );

        $this->assertFalse($request->matches([CapabilityCriteria::BASIC]));
        $this->assertTrue($request->matches([CapabilityCriteria::ADVANCED]));
    }

    public function testExecute(): void
    {
        $handler = function (AIImageRequest $request): AIImageResponse {
            $this->assertInstanceOf(TextToImageAction::class, $request->action);
            $this->assertSame('cute cat', $request->action->prompt);

            return new AIImageResponse($request, ImageFormat::JPEG, 'IMAGE');
        };

        $request = new AIImageRequest(
            new TextToImageAction('cute cat'),
            ImageFormat::JPEG,
            OutputFormat::BASE64,
            new CriteriaCollection([CapabilityCriteria::BASIC]),
            $handler,
        );

        $response = $request->execute();
        $this->assertSame('IMAGE', $response->resource);
    }
}
