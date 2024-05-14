<?php

namespace ModelflowAi\Image\Tests\Functional;

use ModelflowAi\Core\DecisionTree\AIModelDecisionTree;
use ModelflowAi\Core\DecisionTree\DecisionRuleInterface;
use ModelflowAi\Image\Adapter\Fake\FakeAdapter;
use ModelflowAi\Image\AIImageRequestHandler;
use ModelflowAi\Image\Middleware\HandleMiddleware;
use ModelflowAi\Image\Request\Value\ImageFormat;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AiImageRequestHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testAiImageRequestHandler()
    {
        $dogFileName = dirname(__DIR__, 2) . '/examples/resources/dog.jpeg';
        $dogFile = \fopen($dogFileName, 'r');
        if (!$dogFile) {
            throw new \RuntimeException('Could not open image "dog.jpeg"');
        }

        $catFileName = dirname(__DIR__, 2) . '/examples/resources/cat.png';
        $catFile = \fopen($catFileName, 'r');
        if (!$catFile) {
            throw new \RuntimeException('Could not open image "cat.png"');
        }

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

        $decisionTree = new AIModelDecisionTree([$rule->reveal()]);
        $handler = new HandleMiddleware($decisionTree);

        $requestHandler = new AIImageRequestHandler($handler);

        $response = $requestHandler->createRequest()
            ->textToImage('cute cat')
            ->imageFormat(ImageFormat::PNG)
            ->build()
            ->execute();

        $this->assertSame(base64_encode(file_get_contents($catFileName)), $response->base64());
    }
}
