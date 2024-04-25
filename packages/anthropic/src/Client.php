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

use ModelflowAi\Anthropic\Resources\Messages;
use ModelflowAi\Anthropic\Resources\MessagesInterface;
use ModelflowAi\ApiClient\Transport\TransportInterface;

final readonly class Client implements ClientInterface
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    /**
     * Send a structured list of input messages with text and/or image content, and the model will generate the next message in the conversation.
     *
     * The Messages API can be used for for either single queries or stateless multi-turn conversations.
     *
     * @see https://docs.anthropic.com/claude/reference/messages_post
     */
    public function messages(): MessagesInterface
    {
        return new Messages($this->transport);
    }
}
