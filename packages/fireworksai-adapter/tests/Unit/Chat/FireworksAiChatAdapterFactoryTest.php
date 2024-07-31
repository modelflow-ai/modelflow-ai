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

namespace ModelflowAi\FireworksAiAdapter\Tests\Unit\Chat;

use ModelflowAi\FireworksAiAdapter\Chat\FireworksAiChatAdapter;
use ModelflowAi\FireworksAiAdapter\Chat\FireworksAiChatAdapterFactory;
use OpenAI\Contracts\ClientContract;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FireworksAiChatAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $client = $this->prophesize(ClientContract::class);

        $factory = new FireworksAiChatAdapterFactory($client->reveal());

        $adapter = $factory->createChatAdapter([
            'model' => 'gpt-4',
            'image_to_text' => true,
            'functions' => true,
            'priority' => 0,
        ]);
        $this->assertInstanceOf(FireworksAiChatAdapter::class, $adapter);
    }
}
