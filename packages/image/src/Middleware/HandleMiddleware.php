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

namespace ModelflowAi\Image\Middleware;

use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Response\AIImageResponse;

class HandleMiddleware implements MiddlewareInterface
{
    /**
     * @param DecisionTreeInterface<AIImageRequest, AIImageAdapterInterface> $decisionTree
     */
    public function __construct(
        private readonly DecisionTreeInterface $decisionTree,
    ) {
    }

    public function handleRequest(AIImageRequest $request): AIImageResponse
    {
        /** @var AIImageAdapterInterface $adapter */
        $adapter = $this->decisionTree->determineAdapter($request);

        return $adapter->handleRequest($request);
    }
}
