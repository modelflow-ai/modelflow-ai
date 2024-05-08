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
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Response\AIImageResponse;

class ImageFormatMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly MiddlewareInterface $next,
    ) {
    }

    public function handleRequest(AIImageRequest $request): AIImageResponse
    {
        $response = $this->next->handleRequest($request);
        if ($request->imageFormat !== $response->imageFormat) {
            $response = $this->convertImageFormat($response, $request->imageFormat);
        }

        return $response;
    }

    private function convertImageFormat(
        AIImageResponse $response,
        ImageFormat $imageFormat,
    ): AIImageResponse {
        $image = \imagecreatefromstring((string) \stream_get_contents($response->stream()));
        if (false === $image) {
            return $response;
        }

        $newImage = \imagecreatetruecolor(
            \imagesx($image),
            \imagesy($image),
        );
        if (false === $newImage) {
            return $response;
        }

        \imagecopy($newImage, $image, 0, 0, 0, 0, \imagesx($image), \imagesy($image));

        \ob_start();
        if (ImageFormat::PNG === $imageFormat) {
            \imagepng($newImage);
        }

        if (ImageFormat::JPEG === $imageFormat) {
            \imagejpeg($newImage);
        }

        if (ImageFormat::WEBP === $imageFormat) {
            \imagewebp($newImage);
        }

        $image = \ob_get_clean();

        /** @var resource $resource */
        $resource = \fopen('php://memory', 'w');
        \fwrite($resource, (string) $image);
        \rewind($resource);

        return new AIImageResponse($response->request, $imageFormat, $resource);
    }
}
