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
use ModelflowAi\OllamaAdapter\Embeddings\OllamaEmbeddingAdapter;
use ModelflowAi\OllamaAdapter\OllamaEmbeddingsAdapterFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class OllamaEmbeddingsAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateEmbeddingAdapter(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $factory = new OllamaEmbeddingsAdapterFactory($client->reveal());

        $adapter = $factory->createEmbeddingAdapter([
            'model' => 'llama2',
        ]);
        $this->assertInstanceOf(OllamaEmbeddingAdapter::class, $adapter);
    }
}
