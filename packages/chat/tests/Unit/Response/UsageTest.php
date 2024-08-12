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

namespace ModelflowAi\Chat\Tests\Unit\Response;

use ModelflowAi\Chat\Response\Usage;
use PHPUnit\Framework\TestCase;

class UsageTest extends TestCase
{
    public function testInputTokens(): void
    {
        $usage = new Usage(1, 2, 3);
        $this->assertSame(1, $usage->inputTokens);
    }

    public function testOutputTokens(): void
    {
        $usage = new Usage(1, 2, 3);
        $this->assertSame(2, $usage->outputTokens);
    }

    public function testTotalTokens(): void
    {
        $usage = new Usage(1, 2, 3);
        $this->assertSame(3, $usage->totalTokens);
    }
}
