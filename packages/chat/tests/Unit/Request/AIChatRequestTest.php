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

namespace ModelflowAi\Chat\Tests\Unit\Request;

use ModelflowAi\Chat\Request\AIChatMessageCollection;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ImageBase64Part;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\DecisionTree\Criteria\FeatureCriteria;
use ModelflowAi\DecisionTree\Criteria\PrivacyCriteria;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AIChatRequestTest extends TestCase
{
    use ProphecyTrait;

    public function testConstructor(): void
    {
        $message = new AIChatMessage(
            AIChatMessageRoleEnum::USER,
            new ImageBase64Part('iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==', 'image/jpeg'), // This is a 1x1 pixel white image in base64 format
        );
        $messages = new AIChatMessageCollection($message);
        $criteria = new CriteriaCollection();
        $requestHandler = fn ($request) => null;

        $request = new AIChatRequest($messages, $criteria, [], [], [], $requestHandler);

        $this->assertTrue($request->matches([FeatureCriteria::IMAGE_TO_TEXT]));
    }

    public function testConstructorWithStreamed(): void
    {
        $message = new AIChatMessage(
            AIChatMessageRoleEnum::USER,
            'Test 123',
        );
        $messages = new AIChatMessageCollection($message);
        $criteria = new CriteriaCollection();
        $requestHandler = fn ($request) => null;

        $request = new AIChatRequest($messages, $criteria, [], [], ['streamed' => true], $requestHandler);

        $this->assertTrue($request->matches([FeatureCriteria::STREAM]));
    }

    public function testExecute(): void
    {
        $message1 = new AIChatMessage(AIChatMessageRoleEnum::USER, 'Test content 1');
        $message2 = new AIChatMessage(AIChatMessageRoleEnum::USER, 'Test content 2');
        $criteriaCollection = new CriteriaCollection();

        $requestHandler = fn ($request) => new AIChatResponse($request, new AIChatResponseMessage(AIChatMessageRoleEnum::ASSISTANT, 'Response content 1'));
        $request = new AIChatRequest(new AIChatMessageCollection($message1, $message2), $criteriaCollection, [], [], [], $requestHandler);

        $response = $request->execute();

        $this->assertInstanceOf(AIChatResponse::class, $response);
        $this->assertSame($request, $response->getRequest());
        $this->assertSame('Response content 1', $response->getMessage()->content);
    }

    public function testMatches(): void
    {
        $criteria1 = CapabilityCriteria::BASIC;
        $criteria2 = PrivacyCriteria::LOW;
        $criteriaCollection = new CriteriaCollection([$criteria1, $criteria2]);

        $requestHandler = fn () => null;
        $request = new AIChatRequest(new AIChatMessageCollection(), $criteriaCollection, [], [], [], $requestHandler);

        $this->assertTrue($request->matches([CapabilityCriteria::BASIC]));
        $this->assertTrue($request->matches([PrivacyCriteria::HIGH]));
    }

    public function testOptions(): void
    {
        $criteria1 = CapabilityCriteria::BASIC;
        $criteria2 = PrivacyCriteria::HIGH;
        $criteriaCollection = new CriteriaCollection([$criteria1, $criteria2]);

        $requestHandler = fn () => null;
        $request = new AIChatRequest(new AIChatMessageCollection(), $criteriaCollection, [], [], ['format' => 'json'], $requestHandler);

        $this->assertSame('json', $request->getOption('format'));
    }
}
