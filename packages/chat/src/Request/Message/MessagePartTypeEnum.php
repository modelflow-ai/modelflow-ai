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

namespace ModelflowAi\Chat\Request\Message;

enum MessagePartTypeEnum: string
{
    case TEXT = 'text';
    case BASE64_IMAGE = 'base64-image';
    case TOOL_CALLS = 'tool-calls';
    case TOOL_CALL = 'tool-call';

    case CUSTOM = 'custom';
}
