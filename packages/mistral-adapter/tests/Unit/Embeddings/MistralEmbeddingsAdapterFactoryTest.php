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

namespace ModelflowAi\MistralAdapter\Tests\Unit;

use ModelflowAi\Mistral\ClientInterface;
use ModelflowAi\Mistral\Model;
use ModelflowAi\MistralAdapter\Embeddings\MistralEmbeddingAdapter;
use ModelflowAi\MistralAdapter\Embeddings\MistralEmbeddingsAdapterFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MistralEmbeddingsAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateEmbeddingAdapter(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $factory = new MistralEmbeddingsAdapterFactory($client->reveal());

        $adapter = $factory->createEmbeddingAdapter([
            'model' => Model::EMBED->value,
        ]);
        $this->assertInstanceOf(MistralEmbeddingAdapter::class, $adapter);
    }
}
