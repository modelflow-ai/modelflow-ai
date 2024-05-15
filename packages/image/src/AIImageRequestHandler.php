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

use ModelflowAi\Image\Middleware\MiddlewareInterface;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Builder\AIImageRequestBuilder;
use ModelflowAi\Image\Response\AIImageResponse;

final readonly class AIImageRequestHandler implements AIImageRequestHandlerInterface
{
    public function __construct(
        private MiddlewareInterface $middleware,
    ) {
    }

    private function handle(AIImageRequest $request): AIImageResponse
    {
        $response = $this->middleware->handleRequest($request);
        if ($request->imageFormat !== $response->imageFormat) {
            throw new \RuntimeException('Image format mismatch');
        }

        return $response;
    }

    public function createRequest(): AIImageRequestBuilder
    {
        return AIImageRequestBuilder::create(fn (AIImageRequest $request): AIImageResponse => $this->handle($request));
    }
}
