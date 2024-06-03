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

namespace ModelflowAi\OpenaiAdapter;

use ModelflowAi\Chat\Adapter\AIChatAdapterFactoryInterface;
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\OpenaiAdapter\Model\OpenaiChatAdapter;
use OpenAI\Contracts\ClientContract;

final readonly class OpenaiChatAdapterFactory implements AIChatAdapterFactoryInterface
{
    public function __construct(
        private ClientContract $client,
    ) {
    }

    public function createChatAdapter(array $options): AIChatAdapterInterface
    {
        $model = \str_replace('gpt', 'gpt-', (string) $options['model']);

        return new OpenaiChatAdapter(
            $this->client,
            $model,
        );
    }
}
