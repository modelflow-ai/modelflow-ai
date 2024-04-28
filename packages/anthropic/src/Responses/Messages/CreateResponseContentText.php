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
 * @phpstan-import-type TextMessage from \ModelflowAi\Anthropic\Resources\MessagesInterface
 */
final readonly class CreateResponseContentText extends CreateResponseContent
{
    public function __construct(
        string $type,
        public string $text,
    ) {
        parent::__construct($type);
    }

    /**
     * @param TextMessage $attributes
     */
    public static function textFrom(array $attributes): self
    {
        return new self(
            $attributes['type'],
            $attributes['text'],
        );
    }
}
