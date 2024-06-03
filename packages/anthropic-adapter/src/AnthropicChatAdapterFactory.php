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

namespace ModelflowAi\AnthropicAdapter;

use ModelflowAi\Anthropic\ClientInterface;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\AnthropicAdapter\Model\AnthropicChatAdapter;
use ModelflowAi\Chat\Adapter\AIChatAdapterFactoryInterface;
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;

final readonly class AnthropicChatAdapterFactory implements AIChatAdapterFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function createChatAdapter(array $options): AIChatAdapterInterface
    {
        return new AnthropicChatAdapter(
            $this->client,
            Model::from($options['model']),
        );
    }
}
