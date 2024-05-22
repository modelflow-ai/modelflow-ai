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

namespace ModelflowAi\OllamaAdapter;

use ModelflowAi\Embeddings\Adapter\EmbeddingAdapterInterface;
use ModelflowAi\Embeddings\Adapter\EmbeddingsAdapterFactoryInterface;
use ModelflowAi\Ollama\ClientInterface;
use ModelflowAi\OllamaAdapter\Embeddings\OllamaEmbeddingAdapter;

final readonly class OllamaEmbeddingsAdapterFactory implements EmbeddingsAdapterFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function createEmbeddingAdapter(array $options): EmbeddingAdapterInterface
    {
        return new OllamaEmbeddingAdapter(
            $this->client,
            $options['model'],
        );
    }
}
