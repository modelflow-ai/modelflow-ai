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

use ModelflowAi\Anthropic\Responses\Messages\CreateStreamedResponseDelta;
use ModelflowAi\Anthropic\Tests\DataFixtures;
use PHPUnit\Framework\TestCase;

final class CreateStreamedResponseDeltaTest extends TestCase
{
    public function testFrom(): void
    {
        $attributes = DataFixtures::MESSAGES_CREATE_STREAMED_RESPONSES[1]['content'];

        $instance = CreateStreamedResponseDelta::from($attributes);

        $this->assertInstanceOf(CreateStreamedResponseDelta::class, $instance);
        $this->assertSame($attributes['index'], $instance->index);
        $this->assertSame($attributes['type'], $instance->type);
        $this->assertSame($attributes['text'], $instance->text);
    }
}
