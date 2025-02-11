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

use ModelflowAi\Integration\Symfony\Command\ChatCommand;

/*
 * @internal
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('modelflow_ai.command.chat', ChatCommand::class)
        ->args([
            service('modelflow_ai.chat_request_handler'),
        ])
        ->tag('console.command', ['command' => 'modelflow-ai:chat']);
};
