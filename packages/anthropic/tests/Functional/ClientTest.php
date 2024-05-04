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

namespace ModelflowAi\Anthropic\Tests\Functional;

use ModelflowAi\Anthropic\Client;
use ModelflowAi\Anthropic\ClientInterface;
use ModelflowAi\Anthropic\DataFixtures;
use ModelflowAi\ApiClient\Responses\MetaInformation;
use ModelflowAi\ApiClient\Transport\Response\ObjectResponse;
use ModelflowAi\ApiClient\Transport\Testing\MockResponseMatcher;
use ModelflowAi\ApiClient\Transport\Testing\MockTransport;
use ModelflowAi\ApiClient\Transport\Testing\PartialPayload;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testMessages(): void
    {
        $mockResponseMatcher = new MockResponseMatcher();
        $instance = $this->createInstance($mockResponseMatcher);

        $mockResponseMatcher->addResponse(PartialPayload::create(
            'messages',
            DataFixtures::MESSAGES_CREATE_REQUEST,
        ), new ObjectResponse(DataFixtures::MESSAGES_CREATE_RESPONSE, MetaInformation::empty()));

        $response = $instance->messages()->create(DataFixtures::MESSAGES_CREATE_REQUEST_RAW);

        $this->assertSame(DataFixtures::MESSAGES_CREATE_RESPONSE['id'], $response->id);
    }

    private function createInstance(MockResponseMatcher $responseMatcher): ClientInterface
    {
        return new Client(new MockTransport($responseMatcher));
    }
}
