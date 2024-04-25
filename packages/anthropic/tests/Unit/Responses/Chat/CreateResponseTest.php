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

namespace ModelflowAi\Anthropic\Tests\Unit\Responses\Chat;

use ModelflowAi\Anthropic\Responses\Messages\CreateResponse;
use ModelflowAi\Anthropic\Tests\DataFixtures;
use ModelflowAi\ApiClient\Responses\MetaInformation;
use PHPUnit\Framework\TestCase;

final class CreateResponseTest extends TestCase
{
    public function testFrom(): void
    {
        $instance = CreateResponse::from(DataFixtures::MESSAGES_CREATE_RESPONSE, MetaInformation::empty());

        $responseData = DataFixtures::MESSAGES_CREATE_RESPONSE;
        $this->assertSame($responseData['id'], $instance->id);
        $this->assertSame($responseData['type'], $instance->type);
        $this->assertSame($responseData['role'], $instance->role);
        $this->assertSame($responseData['model'], $instance->model);
        $this->assertSame($responseData['stop_sequence'], $instance->stopSequence);
        $this->assertSame($responseData['usage']['input_tokens'], $instance->usage->promptTokens);
        $this->assertSame($responseData['usage']['output_tokens'], $instance->usage->completionTokens);
        $this->assertSame($responseData['usage']['input_tokens'] + $responseData['usage']['output_tokens'], $instance->usage->totalTokens);
        $this->assertSame($responseData['content'][0]['type'], $instance->content[0]->type);
        $this->assertSame($responseData['content'][0]['text'], $instance->content[0]->text);
        $this->assertSame($responseData['stop_reason'], $instance->stopReason);
        $this->assertInstanceOf(MetaInformation::class, $instance->meta);
    }
}
