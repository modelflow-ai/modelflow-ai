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
use ModelflowAi\Anthropic\Tests\DataFixtures;
use PHPUnit\Framework\TestCase;

final class CreateResponseContentTest extends TestCase
{
    public function testFrom(): void
    {
        $data = DataFixtures::MESSAGES_CREATE_RESPONSE['content'][0];

        $instance = CreateResponseContent::from($data);

        $this->assertInstanceOf(CreateResponseContent::class, $instance);
        $this->assertSame($data['type'], $instance->type);
        $this->assertSame($data['text'], $instance->text);
    }
}
