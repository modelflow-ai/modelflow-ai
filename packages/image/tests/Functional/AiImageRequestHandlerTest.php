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

namespace ModelflowAi\Image\Tests\Functional;

use ModelflowAi\Core\DecisionTree\AIModelDecisionTree;
use ModelflowAi\Core\DecisionTree\AIModelDecisionTreeInterface;
use ModelflowAi\Core\DecisionTree\DecisionRuleInterface;
use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Adapter\Fake\FakeAdapter;
use ModelflowAi\Image\AIImageRequestHandler;
use ModelflowAi\Image\Middleware\HandleMiddleware;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Tests\ResourceTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AiImageRequestHandlerTest extends TestCase
{
    use ProphecyTrait;
    use ResourceTrait;

    public function testRequestResponse(): void
    {
        $dogFile = $this->getDogImageResource();
        $catFile = $this->getCatImageResource();

        $adapter = new FakeAdapter([
            [
                'prompt' => 'cute dog',
                'imageFormat' => ImageFormat::JPEG,
                'image' => $dogFile,
            ],
            [
                'prompt' => 'cute cat',
                'imageFormat' => ImageFormat::PNG,
                'image' => $catFile,
            ],
        ]);

        $rule = $this->prophesize(DecisionRuleInterface::class);
        $rule->matches(Argument::any())->willReturn(true);
        $rule->getAdapter()->willReturn($adapter);

        /** @var AIModelDecisionTreeInterface<AIImageRequest, AIImageAdapterInterface> $decisionTree */
        $decisionTree = new AIModelDecisionTree([$rule->reveal()]);
        $handler = new HandleMiddleware($decisionTree);

        $requestHandler = new AIImageRequestHandler($handler);

        $response = $requestHandler->createRequest()
            ->textToImage('cute cat')
            ->imageFormat(ImageFormat::PNG)
            ->build()
            ->execute();

        $this->assertSame($this->getCatImageBase64(), $response->base64());
    }
}
