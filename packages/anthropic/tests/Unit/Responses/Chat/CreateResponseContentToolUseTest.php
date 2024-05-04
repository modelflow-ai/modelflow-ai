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

use ModelflowAi\Anthropic\DataFixtures;
use ModelflowAi\Anthropic\Responses\Messages\CreateResponseContentToolUse;
use PHPUnit\Framework\TestCase;

final class CreateResponseContentToolUseTest extends TestCase
{
    public function testFromToolUseType(): void
    {
        $data = DataFixtures::MESSAGES_CREATE_WITH_TOOLS_RESPONSE['content'][0];

        $instance = CreateResponseContentToolUse::from($data);

        $this->assertInstanceOf(CreateResponseContentToolUse::class, $instance);
        $this->assertSame($data['id'], $instance->id);
        $this->assertSame($data['name'], $instance->name);
        $this->assertSame($data['input'], $instance->input);
    }
}
