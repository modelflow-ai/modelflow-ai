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

namespace ModelflowAi\Chat;

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Builder\AIChatRequestBuilder;
use ModelflowAi\Chat\Request\Builder\AIChatStreamedRequestBuilder;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\ResponseFormat\ResponseFormatInterface;
use ModelflowAi\Chat\Request\ResponseFormat\SupportsResponseFormatInterface;
use ModelflowAi\Chat\Response\AIChatResponse;
use ModelflowAi\Chat\Response\AIChatResponseStream;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use Webmozart\Assert\Assert;

final readonly class AIChatRequestHandler implements AIChatRequestHandlerInterface
{
    /**
     * @param DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface> $decisionTree
     */
    public function __construct(
        private DecisionTreeInterface $decisionTree,
    ) {
    }

    private function handle(AIChatRequest $request): AIChatResponse
    {
        $adapter = $this->decisionTree->determineAdapter($request);

        $responseFormat = $request->getResponseFormat();
        if ($responseFormat instanceof ResponseFormatInterface) {
            Assert::isInstanceOf($responseFormat, ResponseFormatInterface::class);

            if (!$adapter instanceof SupportsResponseFormatInterface
                || !$adapter->supportsResponseFormat($responseFormat)
            ) {
                $request->getMessages()->addResponseFormat($responseFormat);
            }
        }

        return $adapter->handleRequest($request);
    }

    public function createRequest(AIChatMessage ...$messages): AIChatRequestBuilder
    {
        return AIChatRequestBuilder::create(function (AIChatRequest $request): AIChatResponse {
            $response = $this->handle($request);
            Assert::isInstanceOf($response, AIChatResponse::class);

            return $response;
        })->addMessages($messages);
    }

    public function createStreamedRequest(AIChatMessage ...$messages): AIChatStreamedRequestBuilder
    {
        return AIChatStreamedRequestBuilder::create(function (AIChatRequest $request): AIChatResponseStream {
            $response = $this->handle($request);
            Assert::isInstanceOf($response, AIChatResponseStream::class);

            return $response;
        })->addMessages($messages);
    }
}
