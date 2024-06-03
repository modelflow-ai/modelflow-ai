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
use ModelflowAi\OllamaAdapter\Model\OllamaCompletionAdapter;
use ModelflowAi\OllamaAdapter\OllamaCompletionAdapterFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class OllamaCompletionAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateCompletionAdapter(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $factory = new OllamaCompletionAdapterFactory($client->reveal());

        $adapter = $factory->createCompletionAdapter([
            'model' => 'llama2',
            'image_to_text' => true,
            'functions' => true,
            'priority' => 0,
        ]);
        $this->assertInstanceOf(OllamaCompletionAdapter::class, $adapter);
    }
}
