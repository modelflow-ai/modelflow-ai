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

namespace ModelflowAi\Embeddings\Adapter\Fake;

use ModelflowAi\Embeddings\Adapter\EmbeddingAdapterInterface;

class FakeAdapter implements EmbeddingAdapterInterface
{
    /**
     * @param array<string, float[]> $embeddings
     */
    public function __construct(
        private readonly array $embeddings,
    ) {
    }

    public function embedText(string $text): array
    {
        if (!\array_key_exists($text, $this->embeddings)) {
            throw new \RuntimeException(\sprintf('Text "%s" not found in embeddings.', $text));
        }

        return $this->embeddings[$text];
    }
}
