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

namespace ModelflowAi\Anthropic;

enum Model: string
{
    case CLAUDE_3_OPUS = 'claude-3-opus-20240229';
    case CLAUDE_3_5_SONNET = 'claude-3-5-sonnet-20241022';
    case CLAUDE_3_SONNET = 'claude-3-sonnet-20240229';
    case CLAUDE_3_HAIKU = 'claude-3-haiku-20240307';

    public function jsonSupported(): bool
    {
        return true;
    }

    public function toolsSupported(): bool
    {
        return true;
    }
}
