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
final readonly class CreateResponseContentToolUse extends CreateResponseContent
{
    /**
     * @param array<string, mixed> $input
     */
    private function __construct(
        string $type,
        public string $id,
        public string $name,
        public array $input,
    ) {
        parent::__construct($type);
    }

    /**
     * @param ToolUseMessage $attributes
     */
    public static function toolUseFrom(array $attributes): self
    {
        return new self(
            $attributes['type'],
            $attributes['id'],
            $attributes['name'],
            $attributes['input'],
        );
    }
}
