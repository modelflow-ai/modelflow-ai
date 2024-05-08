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

namespace ModelflowAi\Image\Middleware;

use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Response\AIImageResponse;

interface MiddlewareInterface
{
    public function handleRequest(AIImageRequest $request): AIImageResponse;
}
