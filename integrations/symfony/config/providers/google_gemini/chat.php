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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ModelflowAi\GoogleGeminiAdapter\Chat\GoogleGeminiChatAdapterFactory;

/*
 * @internal
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('modelflow_ai.providers.google_gemini.chat_adapter_factory', GoogleGeminiChatAdapterFactory::class)
        ->args([
            service('modelflow_ai.providers.google_gemini.client'),
        ]);
};
