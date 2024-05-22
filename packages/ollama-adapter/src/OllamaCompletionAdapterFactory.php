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

namespace ModelflowAi\OllamaAdapter;

use ModelflowAi\Core\Factory\CompletionAdapterFactoryInterface;
use ModelflowAi\Core\Model\AIModelAdapterInterface;
use ModelflowAi\Ollama\ClientInterface;
use ModelflowAi\OllamaAdapter\Model\OllamaCompletionModelAdapter;

final readonly class OllamaCompletionAdapterFactory implements CompletionAdapterFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function createCompletionAdapter(array $options): AIModelAdapterInterface
    {
        return new OllamaCompletionModelAdapter(
            $this->client,
            $options['model'],
        );
    }
}
