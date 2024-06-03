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

namespace ModelflowAi\Completion\Response;

use ModelflowAi\Completion\Request\AICompletionRequest;

readonly class AICompletionResponse
{
    public function __construct(
        private AICompletionRequest $request,
        private string $content,
    ) {
    }

    public function getRequest(): AICompletionRequest
    {
        return $this->request;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
