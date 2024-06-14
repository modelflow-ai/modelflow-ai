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

namespace ModelflowAi\OpenaiAdapter\Tests\Unit\Embeddings;

use ModelflowAi\OpenaiAdapter\Embeddings\OpenaiEmbeddingAdapter;
use ModelflowAi\OpenaiAdapter\Embeddings\OpenaiEmbeddingsAdapterFactory;
use OpenAI\Contracts\ClientContract;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class OpenaiEmbeddingsAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateEmbeddingAdapter(): void
    {
        $client = $this->prophesize(ClientContract::class);

        $factory = new OpenaiEmbeddingsAdapterFactory($client->reveal());

        $adapter = $factory->createEmbeddingAdapter([
            'model' => 'gpt-4',
        ]);
        $this->assertInstanceOf(OpenaiEmbeddingAdapter::class, $adapter);
    }
}
