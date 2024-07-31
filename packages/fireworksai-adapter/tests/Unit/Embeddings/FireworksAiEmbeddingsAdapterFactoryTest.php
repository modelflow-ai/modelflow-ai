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

namespace ModelflowAi\FireworksAiAdapter\Tests\Unit\Embeddings;

use ModelflowAi\FireworksAiAdapter\Embeddings\FireworksAiEmbeddingAdapter;
use ModelflowAi\FireworksAiAdapter\Embeddings\FireworksAiEmbeddingsAdapterFactory;
use OpenAI\Contracts\ClientContract;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FireworksAiEmbeddingsAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateEmbeddingAdapter(): void
    {
        $client = $this->prophesize(ClientContract::class);

        $factory = new FireworksAiEmbeddingsAdapterFactory($client->reveal());

        $adapter = $factory->createEmbeddingAdapter([
            'model' => 'gpt-4',
        ]);
        $this->assertInstanceOf(FireworksAiEmbeddingAdapter::class, $adapter);
    }
}
