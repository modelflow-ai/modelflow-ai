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

namespace ModelflowAi\Anthropic\Resources;

use ModelflowAi\Anthropic\Responses\Messages\CreateResponse;
use ModelflowAi\Anthropic\Responses\Messages\CreateStreamedResponse;

/**
 * @phpstan-type TextMessage array{type: "text", text: string}
 * @phpstan-type ImageMessage array{type: "image", source: array{type: "base64", media_type: string, data: string}}
 * @phpstan-type MessageContent string|TextMessage|ImageMessage
 * @phpstan-type Message array{role: "system"|"assistant"|"user", content: MessageContent}
 */
interface MessagesInterface
{
    /**
     * @param array{
     *     model: string,
     *     messages: Message[],
     *     max_tokens: int,
     *     metadata?: array{user_id: string},
     *     stop_sequences?: string[],
     *     temperature?: float,
     *     top_k?: int,
     *     top_p?: float,
     * } $parameters
     */
    public function create(array $parameters): CreateResponse;

    /**
     * @param array{
     *      model: string,
     *      messages: Message[],
     *      max_tokens: int,
     *      metadata?: array{user_id: string},
     *      stop_sequences?: string[],
     *      temperature?: float,
     *      top_k?: int,
     *      top_p?: float,
     * } $parameters
     *
     * @return \Iterator<int, CreateStreamedResponse>
     */
    public function createStreamed(array $parameters): \Iterator;
}
