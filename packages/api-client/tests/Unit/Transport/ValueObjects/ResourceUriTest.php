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

namespace ModelflowAi\ApiClient\Tests\Unit\Transport\ValueObjects;

use ModelflowAi\ApiClient\Transport\ValueObjects\ResourceUri;
use PHPUnit\Framework\TestCase;

final class ResourceUriTest extends TestCase
{
    public function testConstructor(): void
    {
        $uri = 'chat/completions';
        $resourceUri = new ResourceUri($uri);

        $this->assertSame($uri, $resourceUri->uri);
        $this->assertSame($uri, $resourceUri->__toString());
    }

    public function testGet(): void
    {
        $uri = 'chat/completions';
        $resourceUri = ResourceUri::get($uri);

        $this->assertSame($uri, $resourceUri->uri);
        $this->assertSame($uri, $resourceUri->__toString());
    }

    public function testEquals(): void
    {
        $uri = 'chat/completions';
        $resourceUri1 = ResourceUri::get($uri);
        $resourceUri2 = ResourceUri::get($uri);
        $resourceUri3 = ResourceUri::get('chat/messages');

        $this->assertTrue($resourceUri1->equals($resourceUri2));
        $this->assertFalse($resourceUri1->equals($resourceUri3));
    }
}
