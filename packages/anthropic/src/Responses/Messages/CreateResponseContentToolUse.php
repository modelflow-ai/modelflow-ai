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

/**
 * @phpstan-import-type ToolUseMessage from \ModelflowAi\Anthropic\Resources\MessagesInterface
 */
final readonly class CreateResponseContentToolUse
{
    /**
     * @param array<string, mixed> $input
     */
    private function __construct(
        public string $id,
        public string $name,
        public array $input,
    ) {
    }

    /**
     * @param ToolUseMessage $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['id'],
            $attributes['name'],
            $attributes['input'],
        );
    }
}
