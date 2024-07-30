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

namespace ModelflowAi\FireworksAiAdapter\Image;

use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Request\Action\TextToImageAction;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Response\AIImageResponse;
use Symfony\Component\HttpClient\Response\StreamableInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FireworksAiImageGenerationAdapter implements AIImageAdapterInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $model = 'stable-diffusion-xl-1024-v1-0',
    ) {
    }

    public function handleRequest(AIImageRequest $request): AIImageResponse
    {
        /** @var TextToImageAction $action */
        $action = $request->action;

        $accept = match ($request->imageFormat) {
            ImageFormat::PNG => 'image/png',
            ImageFormat::JPEG => 'image/jpeg',
            default => throw new \RuntimeException('Unsupported image format'),
        };

        /** @var StreamableInterface $response */
        $response = $this->httpClient->request('POST', $this->model, [
            'headers' => [
                'Accept' => $accept,
            ],
            'json' => [
                'cfg_scale' => 7,
                'height' => 1024,
                'width' => 1024,
                'steps' => 30,
                'seed' => 0,
                'safety_check' => false,
                'prompt' => $action->prompt,
            ],
        ]);

        return new AIImageResponse($request, $request->imageFormat, $response->toStream());
    }

    public function supports(object $request): bool
    {
        if (!$request instanceof AIImageRequest) {
            return false;
        }

        return $request->action instanceof TextToImageAction
            && ImageFormat::WEBP !== $request->imageFormat;
    }
}
