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

namespace ModelflowAi\GoogleGeminiAdapter\Tests\Unit\Chat;

use Gemini\Contracts\ClientContract;
use Gemini\Enums\ModelType;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use Gemini\Testing\ClientFake;
use ModelflowAi\Chat\Request\AIChatMessageCollection;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\AIChatStreamedRequest;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseStream;
use ModelflowAi\DecisionTree\Criteria\CriteriaCollection;
use ModelflowAi\GoogleGeminiAdapter\Chat\GoogleGeminiChatAdapter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class GoogleGeminiChatAdapterTest extends TestCase
{
    use ProphecyTrait;

    public function testSupports(): void
    {
        $client = $this->prophesize(ClientContract::class);

        $adapter = new GoogleGeminiChatAdapter($client->reveal(), ModelType::GEMINI_FLASH->value);

        $request = new AIChatRequest(
            new AIChatMessageCollection(
                new AIChatMessage(AIChatMessageRoleEnum::USER, 'some text'),
            ),
            new CriteriaCollection(),
            [],
            [],
            [],
            fn () => null,
        );

        $this->assertTrue($adapter->supports($request));
    }

    public function testHandleRequest(): void
    {
        $client = new ClientFake([
            GenerateContentResponse::fake([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'success',
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $request = new AIChatRequest(
            new AIChatMessageCollection(
                new AIChatMessage(
                    AIChatMessageRoleEnum::SYSTEM,
                    'Hello',
                ),
                new AIChatMessage(
                    AIChatMessageRoleEnum::USER,
                    'World!',
                ),
            ),
            new CriteriaCollection(),
            [],
            [],
            [],
            fn () => null,
        );

        $adapter = new GoogleGeminiChatAdapter($client, ModelType::GEMINI_FLASH->value);
        $result = $adapter->handleRequest($request);

        $this->assertInstanceOf(AIChatResponse::class, $result);
        $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $result->getMessage()->role);
        $this->assertSame('success', $result->getMessage()->content);

        $client->generativeModel(ModelType::GEMINI_FLASH->value)
            ->assertSent(
                fn (string $methods, array $args) => 'generateContent' === $methods
                    && 'Hello' === $args[0]->parts[0]->text
                    && 'World!' === $args[1]->parts[0]->text,
            );
    }

    public function testHandleRequestWithOptions(): void
    {
        $client = new ClientFake([
            GenerateContentResponse::fake([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'success',
                                ],
                            ],
                        ],
                    ],
                ],
                'usageMetadata' => [
                    'promptTokenCount' => 150,
                    'totalTokenCount' => 200,
                ],
            ]),
        ]);

        $request = new AIChatRequest(new AIChatMessageCollection(
            new AIChatMessage(
                AIChatMessageRoleEnum::SYSTEM,
                'Hello',
            ),
            new AIChatMessage(
                AIChatMessageRoleEnum::USER,
                'World!',
            ),
        ), new CriteriaCollection(), [], [], [
            'temperature' => 0.5,
        ], fn () => null);

        $adapter = new GoogleGeminiChatAdapter($client, ModelType::GEMINI_FLASH->value);
        $result = $adapter->handleRequest($request);

        $this->assertInstanceOf(AIChatResponse::class, $result);
        $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $result->getMessage()->role);
        $this->assertSame('success', $result->getMessage()->content);
        $this->assertSame(150, $result->getUsage()?->inputTokens);
        $this->assertSame(50, $result->getUsage()->outputTokens);
        $this->assertSame(200, $result->getUsage()->totalTokens);

        $client->generativeModel(ModelType::GEMINI_FLASH->value)
            ->assertSent(
                fn (string $methods, array $args) => 'generateContent' === $methods
                    && 'Hello' === $args[0]->parts[0]->text
                    && 'World!' === $args[1]->parts[0]->text,
            );
    }

    public function testHandleRequestStreamed(): void
    {
        $client = new ClientFake([ // @phpstan-ignore-line
            GenerateContentResponse::fakeStream(\fopen(__DIR__ . '/Fixtures/stream.json', 'r')), // @phpstan-ignore-line
        ]);

        $request = new AIChatStreamedRequest(new AIChatMessageCollection(
            new AIChatMessage(
                AIChatMessageRoleEnum::SYSTEM,
                'Hello',
            ),
            new AIChatMessage(
                AIChatMessageRoleEnum::USER,
                'World!',
            ),
        ), new CriteriaCollection(), [], [], [], fn () => null);

        $adapter = new GoogleGeminiChatAdapter($client, ModelType::GEMINI_FLASH->value);
        $result = $adapter->handleRequest($request);

        $this->assertInstanceOf(AIChatResponseStream::class, $result);
        $contents = ['Hello', '!'];
        foreach ($result->getMessageStream() as $i => $response) {
            $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $response->role);
            $this->assertSame($contents[$i], $response->content);
        }
    }
}
