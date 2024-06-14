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

namespace ModelflowAi\MistralAdapter\Embeddings;

use ModelflowAi\Embeddings\Adapter\EmbeddingAdapterInterface;
use ModelflowAi\Embeddings\Adapter\EmbeddingsAdapterFactoryInterface;
use ModelflowAi\Mistral\ClientInterface;
use ModelflowAi\Mistral\Model;

final readonly class MistralEmbeddingsAdapterFactory implements EmbeddingsAdapterFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function createEmbeddingAdapter(array $options): EmbeddingAdapterInterface
    {
        return new MistralEmbeddingAdapter(
            $this->client,
            Model::from(Model::EMBED->value),
        );
    }
}
