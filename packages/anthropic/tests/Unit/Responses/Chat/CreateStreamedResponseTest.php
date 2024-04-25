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

namespace ModelflowAi\Anthropic\tests\Unit\Responses\Chat;

use ModelflowAi\Anthropic\Responses\Messages\CreateStreamedResponse;
use ModelflowAi\Anthropic\Tests\DataFixtures;
use ModelflowAi\ApiClient\Responses\MetaInformation;
use PHPUnit\Framework\TestCase;

final class CreateStreamedResponseTest extends TestCase
{
    public function testFrom(): void
    {
        $attributes = DataFixtures::MESSAGES_CREATE_STREAMED_RESPONSES[1];

        $instance = CreateStreamedResponse::from(1, $attributes, MetaInformation::empty());

        $this->assertSame(1, $instance->index);
        $this->assertSame($attributes['id'], $instance->id);
        $this->assertSame($attributes['type'], $instance->type);
        $this->assertSame($attributes['role'], $instance->role);
        $this->assertSame($attributes['model'], $instance->model);
        $this->assertSame($attributes['stop_sequence'], $instance->stopSequence);
        $this->assertSame($attributes['usage']['input_tokens'], $instance->usage->promptTokens);
        $this->assertSame($attributes['usage']['output_tokens'], $instance->usage->completionTokens);
        $this->assertSame($attributes['usage']['input_tokens'] + $attributes['usage']['output_tokens'], $instance->usage->totalTokens);
        $this->assertSame($attributes['content']['index'], $instance->content?->index);
        $this->assertSame($attributes['content']['type'], $instance->content->type);
        $this->assertSame($attributes['content']['text'], $instance->content->text);
        $this->assertSame($attributes['stop_reason'], $instance->stopReason);
    }
}
