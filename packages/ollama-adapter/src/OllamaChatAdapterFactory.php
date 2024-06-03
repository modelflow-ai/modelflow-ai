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

use ModelflowAi\Chat\Adapter\AIChatAdapterFactoryInterface;
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Ollama\ClientInterface;
use ModelflowAi\OllamaAdapter\Model\OllamaChatAdapter;

final readonly class OllamaChatAdapterFactory implements AIChatAdapterFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function createChatAdapter(array $options): AIChatAdapterInterface
    {
        return new OllamaChatAdapter(
            $this->client,
            $options['model'],
        );
    }
}
