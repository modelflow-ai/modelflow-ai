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
use ModelflowAi\Integration\Symfony\Criteria\ModelCriteria;
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
     * @param string $model  model to generate the image with. Possible values are: 'dall-e-2', 'dall-e-3' or 'stable-diffusion-xl-1024-v1-0'
     *
     * @return string url to the generated image
     */
    public function generateImage(string $prompt, string $model = 'stable-diffusion-xl-1024-v1-0'): string
    {
        $response = $this->imageRequestHandler->createRequest()
            ->addCriteria(ModelCriteria::tryFrom($model) ?? ModelCriteria::STABLE_DIFFUSSION_XL_1024_FIREWORKS)
            ->imageFormat(ImageFormat::PNG)
            ->textToImage($prompt)
            ->asStream()
            ->build()
            ->execute();

        $hash = \md5(\time().$prompt);
        \file_put_contents(\dirname(__DIR__, 2).'/var/images/'.$hash.'.jpeg', \stream_get_contents($response->stream()));

        return $this->urlGenerator->generate('image', ['hash' => $hash], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
