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

namespace ModelflowAi\Chat;

use ModelflowAi\Chat\Request\Builder\AIChatRequestBuilder;
use ModelflowAi\Chat\Request\Builder\AIChatStreamedRequestBuilder;
use ModelflowAi\Chat\Request\Message\AIChatMessage;

interface AIChatRequestHandlerInterface
{
    public function createRequest(AIChatMessage ...$messages): AIChatRequestBuilder;

    public function createStreamedRequest(AIChatMessage ...$messages): AIChatStreamedRequestBuilder;
}
