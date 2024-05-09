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

namespace ModelflowAi\Image\Tools;

use ModelflowAi\Image\AIImageRequestHandlerInterface;
use ModelflowAi\Image\Request\Value\ImageFormat;

readonly class ImageGenerationTool
{
    public function __construct(
       private AIImageRequestHandlerInterface $imageRequestHandler,
    ) {
    }

    /**
     * This tool generates an image based on a given prompt.
     *
     * Use this tool in the following circumstances:
     * - User is asking to generate an image.
     *
     * TODO improve this description to be sure that the prompt will be as good as possible.
     * @param string $prompt Well structured prompt to generate the image from.
     *
     * @return string Generated image as base64.
     */
    public function generateImage(string $prompt, string $imageFormat): string
    {
        $imageFormat = ImageFormat::tryFrom($imageFormat) ? ImageFormat::from($imageFormat) : ImageFormat::JPEG;

        $response = $this->imageRequestHandler->createRequest()
            ->imageFormat($imageFormat)
            ->textToImage($prompt)
            ->asStream()
            ->build()
            ->execute();

        // TODO add storage layer for the image and return the URL to the image.

        return $response->stream();
    }
}
