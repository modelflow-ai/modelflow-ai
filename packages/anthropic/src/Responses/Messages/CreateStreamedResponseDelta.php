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

namespace ModelflowAi\Anthropic\Responses\Messages;

final readonly class CreateStreamedResponseDelta
{
    private function __construct(
        public int $index,
        public string $type,
        public string $text,
    ) {
    }

    /**
     * @param array{
     *     index?: int,
     *     type?: "text"|"text_delta",
     *     text?: string,
     * } $attributes
     */
    public static function from(array $attributes): ?self
    {
        if (null === ($attributes['index'] ?? null)
            || null === ($attributes['type'] ?? null)
            || null === ($attributes['text'] ?? null)
        ) {
            return null;
        }

        return new self(
            $attributes['index'],
            $attributes['type'],
            $attributes['text'],
        );
    }
}
