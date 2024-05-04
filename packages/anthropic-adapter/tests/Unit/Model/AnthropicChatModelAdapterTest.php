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

namespace ModelflowAi\AnthropicAdapter\Tests\Unit\Model;

use ModelflowAi\Anthropic\Client;
use ModelflowAi\Anthropic\ClientInterface;
use ModelflowAi\Anthropic\DataFixtures;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\AnthropicAdapter\Model\AnthropicChatModelAdapter;
use ModelflowAi\ApiClient\Responses\MetaInformation;
use ModelflowAi\ApiClient\Transport\Response\ObjectResponse;
use ModelflowAi\ApiClient\Transport\Testing\MockResponseMatcher;
use ModelflowAi\ApiClient\Transport\Testing\MockTransport;
use ModelflowAi\ApiClient\Transport\Testing\PartialPayload;
use ModelflowAi\ApiClient\Transport\Testing\StreamedResponse;
use ModelflowAi\Core\Request\AIChatMessageCollection;
use ModelflowAi\Core\Request\AIChatRequest;
use ModelflowAi\Core\Request\Criteria\AIRequestCriteriaCollection;
use ModelflowAi\Core\Request\Message\AIChatMessage;
use ModelflowAi\Core\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Core\Response\AIChatResponse;
use ModelflowAi\Core\Response\AIChatResponseStream;
use ModelflowAi\Core\ToolInfo\ToolInfoBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class AnthropicChatModelAdapterTest extends TestCase
{
    use ProphecyTrait;

    public function testSupports(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $adapter = new AnthropicChatModelAdapter($client->reveal(), Model::CLAUDE_3_SONNET);

        $request = new AIChatRequest(new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'some text'),
        ), new AIRequestCriteriaCollection(), [], [], [], fn () => null);

        $this->assertTrue($adapter->supports($request));
    }

    public function testSupportsWithTools(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $adapter = new AnthropicChatModelAdapter($client->reveal(), Model::CLAUDE_3_SONNET);

        $request = new AIChatRequest(new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'User message'),
        ), new AIRequestCriteriaCollection(), [
            'test' => [$this, 'toolMethod'],
        ], [
            ToolInfoBuilder::buildToolInfo($this, 'toolMethod', 'test'),
        ], [], fn () => null);

        $this->assertFalse($adapter->supports($request));
    }

    public function testHandleRequest(): void
    {
        $mockResponseMatcher = new MockResponseMatcher();
        $mockResponseMatcher->addResponse(PartialPayload::create(
            'messages',
            DataFixtures::MESSAGES_CREATE_REQUEST,
        ), new ObjectResponse(DataFixtures::MESSAGES_CREATE_RESPONSE, MetaInformation::empty()));

        $client = new Client(new MockTransport($mockResponseMatcher));

        $request = new AIChatRequest(new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, DataFixtures::MESSAGES_CREATE_REQUEST_RAW['messages'][0]['content']),
            new AIChatMessage(AIChatMessageRoleEnum::USER, DataFixtures::MESSAGES_CREATE_REQUEST_RAW['messages'][1]['content']),
        ), new AIRequestCriteriaCollection(), [], [], [], fn () => null);

        $adapter = new AnthropicChatModelAdapter($client, Model::CLAUDE_3_HAIKU, 100);
        $result = $adapter->handleRequest($request);

        $this->assertInstanceOf(AIChatResponse::class, $result);
        $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $result->getMessage()->role);
        $this->assertSame(DataFixtures::MESSAGES_CREATE_RESPONSE['content'][0]['text'], $result->getMessage()->content);
    }

    public function testHandleRequestStreamed(): void
    {
        $mockResponseMatcher = new MockResponseMatcher();
        $client = new Client(new MockTransport($mockResponseMatcher));

        $responseChunks = [];
        foreach (DataFixtures::MESSAGES_CREATE_STREAMED_RESPONSES_RAW as $response) {
            $responseChunks[] = \implode(\PHP_EOL, $response);
        }

        $mockResponseMatcher->addResponse(PartialPayload::create(
            'messages',
            DataFixtures::MESSAGES_CREATE_STREAMED_REQUEST,
        ), new StreamedResponse($responseChunks, MetaInformation::empty()));

        $request = new AIChatRequest(new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, DataFixtures::MESSAGES_CREATE_REQUEST_RAW['messages'][0]['content']),
            new AIChatMessage(AIChatMessageRoleEnum::USER, DataFixtures::MESSAGES_CREATE_REQUEST_RAW['messages'][1]['content']),
        ), new AIRequestCriteriaCollection(), [], [], ['streamed' => true], fn () => null);

        $adapter = new AnthropicChatModelAdapter($client, Model::CLAUDE_3_HAIKU, 100);
        $result = $adapter->handleRequest($request);

        $this->assertInstanceOf(AIChatResponseStream::class, $result);
        $contents = ['Hello', '!'];
        foreach ($result->getMessageStream() as $i => $response) {
            $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $response->role);
            $this->assertSame($contents[$i], $response->content);
        }
    }

    /**
     * This is a description.
     *
     * @param string $required this is a required parameter
     * @param string $optional this is an optional parameter
     */
    public function toolMethod(string $required, string $optional = ''): string
    {
        return $required . $optional;
    }
}
