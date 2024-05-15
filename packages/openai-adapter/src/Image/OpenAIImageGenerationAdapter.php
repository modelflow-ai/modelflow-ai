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

namespace ModelflowAi\OpenaiAdapter\Image;

use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Request\Action\TextToImageAction;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use ModelflowAi\Image\Request\Value\OutputFormat;
use ModelflowAi\Image\Response\AIImageResponse;
use OpenAI\Contracts\ClientContract;
use OpenAI\Responses\Images\CreateResponseData;
use Symfony\Component\HttpClient\Response\StreamableInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIImageGenerationAdapter implements AIImageAdapterInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ClientContract $client,
        private readonly string $model = 'dall-e-3',
    ) {
    }

    public function handleRequest(AIImageRequest $request): AIImageResponse
    {
        /** @var TextToImageAction $action */
        $action = $request->action;

        $response = $this->client->images()->create([
            'model' => $this->model,
            'prompt' => $action->prompt,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => OutputFormat::BASE64 === $request->outputFormat ? 'b64_json' : 'url',
        ]);

        /** @var CreateResponseData $data */
        $data = $response->data[0];

        if (OutputFormat::BASE64 === $request->outputFormat) {
            return new AIImageResponse($request, ImageFormat::PNG, $data->b64_json);
        }

        /** @var StreamableInterface $response */
        $response = $this->httpClient->request('GET', $data->url);

        return new AIImageResponse($request, ImageFormat::PNG, $response->toStream());
    }

    public function supports(object $request): bool
    {
        if (!$request instanceof AIImageRequest) {
            return false;
        }

        return $request->action instanceof TextToImageAction;
    }
}
