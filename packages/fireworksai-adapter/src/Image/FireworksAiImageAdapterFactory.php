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

use ModelflowAi\Image\Adapter\AIImageAdapterFactoryInterface;
use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class FireworksAiImageAdapterFactory implements AIImageAdapterFactoryInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
    ) {
    }

    public function createImageAdapter(array $options): AIImageAdapterInterface
    {
        return new FireworksAiImageGenerationAdapter(
            $this->httpClient->withOptions([
                'base_uri' => 'https://api.fireworks.ai/inference/v1/image_generation/accounts/fireworks/models/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
            ]),
            $options['model'],
        );
    }
}
