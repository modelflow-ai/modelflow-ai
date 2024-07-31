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

namespace ModelflowAi\FireworksAiAdapter\Completion;

use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\Completion\Response\AICompletionResponse;
use OpenAI\Contracts\ClientContract;
use Webmozart\Assert\Assert;

final readonly class FireworksAiCompletionAdapter implements AICompletionAdapterInterface
{
    public function __construct(
        private ClientContract $client,
        private string $model = 'accounts/fireworks/models/llama-v3-70b-instruct',
        private int $maxTokens = 1024,
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
            'max_tokens' => $this->maxTokens,
        ];

        if ($format) {
            $parameters['format'] = $format;
        }

        $response = $this->client->completions()->create($parameters);

        return new AICompletionResponse($request, $response->choices[0]->text);
    }

    public function supports(object $request): bool
    {
        return $request instanceof AICompletionRequest;
    }
}
