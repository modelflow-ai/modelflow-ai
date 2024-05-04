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

namespace ModelflowAi\AnthropicAdapter\Tests\Unit\Model;

use ModelflowAi\Anthropic\ClientInterface;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\AnthropicAdapter\AnthropicAdapterFactory;
use ModelflowAi\AnthropicAdapter\Model\AnthropicChatModelAdapter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AnthropicAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $factory = new AnthropicAdapterFactory($client->reveal());

        $adapter = $factory->createChatAdapter([
            'model' => Model::CLAUDE_3_SONNET->value,
            'image_to_text' => true,
            'functions' => true,
            'priority' => 0,
        ]);
        $this->assertInstanceOf(AnthropicChatModelAdapter::class, $adapter);
    }
}
