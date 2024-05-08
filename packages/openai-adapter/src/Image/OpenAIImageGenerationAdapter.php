<?php

namespace ModelflowAi\OpenaiAdapter\Image;

use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Task\TextToImageTask;
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
        private HttpClientInterface $httpClient,
        private ClientContract $client,
        private string $model = 'dall-e-3',
    ) {
    }

    public function handleRequest(AIImageRequest $request): AIImageResponse
    {
        /** @var TextToImageTask $task */
        $task = $request->task;

        $response = $this->client->images()->create([
            'model' => $this->model,
            'prompt' => $task->prompt,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => $request->format === OutputFormat::BASE64 ? 'b64_json' : 'url',
        ]);

        /** @var CreateResponseData $data */
        $data = $response->data[0];

        if ($request->format === OutputFormat::BASE64) {
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

        return $request->task instanceof TextToImageTask;
    }
}
