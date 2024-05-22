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

use ModelflowAi\Core\Factory\ChatAdapterFactoryInterface;
use ModelflowAi\Core\Model\AIModelAdapterInterface;
use ModelflowAi\OpenaiAdapter\Model\OpenaiChatModelAdapter;
use OpenAI\Contracts\ClientContract;

final readonly class OpenaiChatAdapterFactory implements ChatAdapterFactoryInterface
{
    public function __construct(
        private ClientContract $client,
    ) {
    }

    public function createChatAdapter(array $options): AIModelAdapterInterface
    {
        $model = \str_replace('gpt', 'gpt-', $options['model']);

        return new OpenaiChatModelAdapter(
            $this->client,
            $model,
        );
    }
}
