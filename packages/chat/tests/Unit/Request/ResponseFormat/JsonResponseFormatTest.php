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

namespace ModelflowAi\Chat\Tests\Unit\Request\ResponseFormat;

use ModelflowAi\Chat\Request\ResponseFormat\JsonResponseFormat;
use PHPUnit\Framework\TestCase;

class JsonResponseFormatTest extends TestCase
{
    public function testType(): void
    {
        $responseFormat = new JsonResponseFormat();

        $this->assertSame('json', $responseFormat->getType());
    }

    public function testAsString(): void
    {
        $responseFormat = new JsonResponseFormat();

        $this->assertSame(<<<Format
It's crucial that your output is a clean JSON object, presented without any additional formatting, annotations, or explanatory content. The response should be ready to use as-is for a system to store it in the database or to process it further.
Format, $responseFormat->asString());
    }
}
