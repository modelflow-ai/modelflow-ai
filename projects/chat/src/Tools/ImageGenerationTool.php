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

namespace App\Tools;

use ModelflowAi\Image\AIImageRequestHandlerInterface;
use ModelflowAi\Image\Request\Value\ImageFormat;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class ImageGenerationTool
{
    public function __construct(
        private AIImageRequestHandlerInterface $imageRequestHandler,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * This tool generates an image based on a given prompt.
     *
     * Use this tool in the following circumstances:
     * - User is asking to generate an image.
     *
     * TODO improve this description to be sure that the prompt will be as good as possible.
     *
     * @param string $prompt well structured prompt to generate the image from
     *
     * @return string url to the generated image
     */
    public function generateImage(string $prompt): string
    {
        $response = $this->imageRequestHandler->createRequest()
            ->imageFormat(ImageFormat::JPEG)
            ->textToImage($prompt)
            ->asStream()
            ->build()
            ->execute();

        $hash = \md5(\time().$prompt);
        \file_put_contents(\dirname(__DIR__, 2).'/var/images/'.$hash.'.jpeg', \stream_get_contents($response->stream()));

        return $this->urlGenerator->generate('image', ['hash' => $hash], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
