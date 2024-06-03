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

namespace ModelflowAi\Completion;

use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\Completion\Request\Builder\AICompletionRequestBuilder;
use ModelflowAi\Completion\Response\AICompletionResponse;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use Webmozart\Assert\Assert;

final readonly class AICompletionRequestHandler implements AICompletionRequestHandlerInterface
{
    /**
     * @param DecisionTreeInterface<AICompletionRequest, AICompletionAdapterInterface> $decisionTree
     */
    public function __construct(
        private DecisionTreeInterface $decisionTree,
    ) {
    }

    private function handle(AICompletionRequest $request): AICompletionResponse
    {
        $adapter = $this->decisionTree->determineAdapter($request);

        return $adapter->handleRequest($request);
    }

    public function createRequest(?string $prompt = null): AICompletionRequestBuilder
    {
        return AICompletionRequestBuilder::create(function (AICompletionRequest $request): AICompletionResponse {
            $response = $this->handle($request);
            Assert::isInstanceOf($response, AICompletionResponse::class);

            return $response;
        })->prompt($prompt);
    }
}
