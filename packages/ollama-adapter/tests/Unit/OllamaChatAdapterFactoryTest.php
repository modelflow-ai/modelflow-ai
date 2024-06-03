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

namespace ModelflowAi\OllamaAdapter\Tests\Unit;

use ModelflowAi\Ollama\ClientInterface;
use ModelflowAi\OllamaAdapter\Model\OllamaChatAdapter;
use ModelflowAi\OllamaAdapter\OllamaChatAdapterFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class OllamaChatAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $factory = new OllamaChatAdapterFactory($client->reveal());

        $adapter = $factory->createChatAdapter([
            'model' => 'llama2',
            'image_to_text' => true,
            'functions' => true,
            'priority' => 0,
        ]);
        $this->assertInstanceOf(OllamaChatAdapter::class, $adapter);
    }
}
