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

namespace ModelflowAi\Anthropic\Responses;

use ModelflowAi\ApiClient\Responses\Usage;

final class UsageFactory
{
    private function __construct()
    {
    }

    /**
     * @param array{
     *     input_tokens: int,
     *     output_tokens?: int,
     * } $attributes
     */
    public static function from(array $attributes): Usage
    {
        $inputTokens = $attributes['input_tokens'];
        $outputTokens = $attributes['output_tokens'] ?? 0;

        return Usage::from([
            'prompt_tokens' => $inputTokens,
            'completion_tokens' => $outputTokens,
            'total_tokens' => $inputTokens + $outputTokens,
        ]);
    }
}
