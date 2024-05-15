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

namespace ModelflowAi\Image\Tests\Unit\Request\Action;

use ModelflowAi\Image\Request\Action\TextToImageAction;
use PHPUnit\Framework\TestCase;

class TextToImageActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new TextToImageAction('prompt');

        $this->assertSame('prompt', $action->prompt);
    }
}
