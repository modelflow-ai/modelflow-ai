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

namespace ModelflowAi\Image\Adapter\Fake;

use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Request\Action\TextToImageAction;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Response\AIImageResponse;

class FakeAdapter implements AIImageAdapterInterface
{
    /**
     * @param array<array{
     *     prompt: string,
     *     imageFormat: ImageFormat,
     *     image: resource,
     * }> $responses
     */
    public function __construct(
        private readonly array $responses,
    ) {
    }

    public function handleRequest(AIImageRequest $request): AIImageResponse
    {
        /** @var TextToImageAction $task */
        $task = $request->action;

        foreach ($this->responses as $response) {
            if ($response['prompt'] === $task->prompt) {
                return new AIImageResponse($request, $response['imageFormat'], $response['image']);
            }
        }

        throw new \RuntimeException('No response found');
    }

    public function supports(object $request): bool
    {
        if (!$request instanceof AIImageRequest) {
            return false;
        }

        return $request->action instanceof TextToImageAction;
    }
}
