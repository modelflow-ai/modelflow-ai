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

namespace ModelflowAi\Image;

use ModelflowAi\Core\DecisionTree\AIModelDecisionTreeInterface;
use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Middleware\HandleMiddleware;
use ModelflowAi\Image\Middleware\ImageFormatMiddleware;
use ModelflowAi\Image\Middleware\MiddlewareInterface;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Builder\AIImageRequestBuilder;
use ModelflowAi\Image\Response\AIImageResponse;

interface AIImageRequestHandlerInterface
{
    public function createRequest(): AIImageRequestBuilder;
}
