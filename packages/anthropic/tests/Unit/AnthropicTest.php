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

namespace ModelflowAi\Anthropic\Tests\Unit;

use ModelflowAi\Anthropic\Anthropic;
use ModelflowAi\Anthropic\ClientInterface;
use ModelflowAi\Anthropic\Factory;
use PHPUnit\Framework\TestCase;

class AnthropicTest extends TestCase
{
    public function testClient(): void
    {
        $this->assertInstanceOf(ClientInterface::class, Anthropic::client('api-key'));
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(Factory::class, Anthropic::factory());
    }
}
