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

use ModelflowAi\Anthropic\Responses\UsageFactory;
use PHPUnit\Framework\TestCase;

class UsageFactoryTest extends TestCase
{
    public function testFrom(): void
    {
        $data = [
            'input_tokens' => 1,
            'output_tokens' => 2,
        ];

        $result = UsageFactory::from($data);
        $this->assertSame(1, $result->promptTokens);
        $this->assertSame(2, $result->completionTokens);
        $this->assertSame(3, $result->totalTokens);
    }
}
