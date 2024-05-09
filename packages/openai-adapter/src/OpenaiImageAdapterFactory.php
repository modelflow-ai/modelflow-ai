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

namespace ModelflowAi\OpenaiAdapter;

use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Adapter\ImageAdapterFactoryInterface;
use ModelflowAi\OpenaiAdapter\Image\OpenAIImageGenerationAdapter;
use OpenAI\Contracts\ClientContract;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenaiImageAdapterFactory implements ImageAdapterFactoryInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private ClientContract $client,
    ) {
    }

    public function createImageAdapter(array $options): AiImageAdapterInterface
    {
        return new OpenAIImageGenerationAdapter(
            $this->httpClient,
            $this->client,
            $options['model'],
        );
    }
}
