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

namespace App;

use ModelflowAi\Chat\AIChatRequestHandlerInterface;
use ModelflowAi\Chat\Request\Builder\AIChatRequestBuilder;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ToolCallsPart;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolExecutor;

require_once __DIR__ . '/WeatherTool.php';

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';
$toolExecutor = new ToolExecutor();

/** @var AIChatRequestBuilder $builder */
$builder = $handler->createRequest()
    ->addUserMessage('How is the weather in hohenems?')
    ->tool('get_current_weather', new WeatherTool(), 'getCurrentWeather')
    ->toolChoice(ToolChoiceEnum::AUTO)
    ->addCriteria(ProviderCriteria::MISTRAL);

$request = $builder->build();
$response = $request->execute();

do {
    $toolCalls = $response->getMessage()->toolCalls;
    if (null !== $toolCalls && 0 < \count($toolCalls)) {
        $builder->addMessage(
            new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, ToolCallsPart::create($toolCalls)),
        );

        foreach ($toolCalls as $toolCall) {
            $builder->addMessage(
                $toolExecutor->execute($request, $toolCall),
            );
        }

        $response = $builder->build()->execute();
    }
} while (null !== $toolCalls && [] !== $toolCalls);

echo $response->getMessage()->content;
