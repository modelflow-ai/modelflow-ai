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

namespace ModelflowAi\FireworksAiAdapter\Chat;

use ModelflowAi\Chat\Adapter\AIChatAdapterFactoryInterface;
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use OpenAI\Contracts\ClientContract;

final readonly class FireworksAiChatAdapterFactory implements AIChatAdapterFactoryInterface
{
    public function __construct(
        private ClientContract $client,
    ) {
    }

    public function createChatAdapter(array $options): AIChatAdapterInterface
    {
        return new FireworksAiChatAdapter(
            $this->client,
            $options['model'],
        );
    }
}
