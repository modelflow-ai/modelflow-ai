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
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\ResponseFormat\ResponseFormatInterface;
use ModelflowAi\Chat\Request\ResponseFormat\SupportsResponseFormatInterface;
use ModelflowAi\Chat\Response\AIChatResponse;
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

        $responseFormat = $request->getOption('responseFormat');
        if ($responseFormat) {
            Assert::isInstanceOf($responseFormat, ResponseFormatInterface::class);

            if ($adapter instanceof SupportsResponseFormatInterface
                && !$adapter->supportResponseFormat($responseFormat)
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
}
