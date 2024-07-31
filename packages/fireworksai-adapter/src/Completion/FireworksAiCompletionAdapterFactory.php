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

use ModelflowAi\Completion\Adapter\AICompletionAdapterFactoryInterface;
use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use OpenAI\Contracts\ClientContract;

final readonly class FireworksAiCompletionAdapterFactory implements AICompletionAdapterFactoryInterface
{
    public function __construct(
        private ClientContract $client,
        private int $maxTokens = 1024,
    ) {
    }

    public function createCompletionAdapter(array $options): AICompletionAdapterInterface
    {
        return new FireworksAiCompletionAdapter(
            $this->client,
            $options['model'],
            $this->maxTokens,
        );
    }
}
