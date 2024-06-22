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

use ModelflowAi\Completion\Adapter\AICompletionAdapterFactoryInterface;
use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Ollama\ClientInterface;

final readonly class OllamaCompletionAdapterFactory implements AICompletionAdapterFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function createCompletionAdapter(array $options): AICompletionAdapterInterface
    {
        return new OllamaCompletionAdapter(
            $this->client,
            $options['model'],
        );
    }
}
