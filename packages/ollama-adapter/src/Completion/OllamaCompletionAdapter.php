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

namespace ModelflowAi\OllamaAdapter\Completion;

use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\Completion\Response\AICompletionResponse;
use ModelflowAi\Ollama\ClientInterface;
use Webmozart\Assert\Assert;

final readonly class OllamaCompletionAdapter implements AICompletionAdapterInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $model = 'llama2',
    ) {
    }

    public function handleRequest(AICompletionRequest $request): AICompletionResponse
    {
        /** @var "json"|null $format */
        $format = $request->getOption('format');
        Assert::inArray($format, [null, 'json'], \sprintf('Invalid format "%s" given.', $format));

        $parameters = [
            'model' => $this->model,
            'prompt' => $request->getPrompt(),
        ];

        if ($format) {
            $parameters['format'] = $format;
        }

        $response = $this->client->completion()->create($parameters);

        return new AICompletionResponse($request, $response->response);
    }

    public function supports(object $request): bool
    {
        return $request instanceof AICompletionRequest;
    }
}
