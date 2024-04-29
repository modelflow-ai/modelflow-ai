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

use ModelflowAi\Anthropic\Responses\Messages\CreateResponseContent;
use ModelflowAi\Anthropic\Responses\Messages\CreateResponseContentToolUse;
use ModelflowAi\Anthropic\Tests\DataFixtures;
use PHPUnit\Framework\TestCase;

final class CreateResponseContentTest extends TestCase
{
    public function testFromTextType(): void
    {
        $data = DataFixtures::MESSAGES_CREATE_RESPONSE['content'][0];

        $instance = CreateResponseContent::from($data);

        $this->assertInstanceOf(CreateResponseContent::class, $instance);
        $this->assertSame($data['type'], $instance->type);
        $this->assertSame($data['text'], $instance->text);
    }

    public function testFromToolUseType(): void
    {
        $data = DataFixtures::MESSAGES_CREATE_WITH_TOOLS_RESPONSE['content'][0];

        $instance = CreateResponseContent::from($data);

        $this->assertInstanceOf(CreateResponseContent::class, $instance);
        $this->assertSame($data['type'], $instance->type);
        $this->assertNull($instance->text);

        $toolUse = $instance->toolUse;
        $this->assertInstanceOf(CreateResponseContentToolUse::class, $toolUse);
        $this->assertSame($data['id'], $toolUse->id);
        $this->assertSame($data['name'], $toolUse->name);
        $this->assertSame($data['input'], $toolUse->input);
    }
}
