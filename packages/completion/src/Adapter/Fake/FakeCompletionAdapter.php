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

namespace ModelflowAi\Completion\Adapter\Fake;

use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\Completion\Response\AICompletionResponse;

class FakeCompletionAdapter implements AICompletionAdapterInterface
{
    /**
     * @var string[]
     */
    private array $contents = [];

    public function addContent(string $content): void
    {
        $this->contents[] = $content;
    }

    public function handleRequest(AICompletionRequest $request): AICompletionResponse
    {
        /** @var string $content */
        $content = \array_shift($this->contents);

        return new AICompletionResponse($request, $content);
    }

    public function supports(object $request): bool
    {
        return $request instanceof AICompletionRequest;
    }
}
