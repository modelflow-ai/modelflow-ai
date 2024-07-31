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

use ModelflowAi\Chat\AIChatRequestHandlerInterface;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ToolCallsPart;
use ModelflowAi\Chat\Response\AIChatResponseStream;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolExecutor;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;

require_once __DIR__ . '/WeatherTool.php';

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$toolExecutor = new ToolExecutor();

$builder = $handler->createRequest()
    ->addUserMessage('How is the weather in hohenems?')
    ->tool('get_current_weather', new WeatherTool(), 'getCurrentWeather')
    ->toolChoice(ToolChoiceEnum::AUTO)
    ->addCriteria(CapabilityCriteria::ADVANCED)
    ->streamed();

$request = $builder->build();

/** @var AIChatResponseStream $response */
$response = $request->execute();

foreach ($response->getMessageStream() as $message) {
    $toolCalls = $message->toolCalls;
    if (null !== $toolCalls && 0 < \count($toolCalls)) {
        $builder->addMessage(
            new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, ToolCallsPart::create($toolCalls)),
        );

        foreach ($toolCalls as $toolCall) {
            $builder->addMessage(
                $toolExecutor->execute($request, $toolCall),
            );
        }
    }
}

/** @var AIChatResponseStream $response */
$response = $builder->build()->execute();
foreach ($response->getMessageStream() as $index => $message) {
    if (0 === $index) {
        echo $message->role->value . ': ';
    }

    echo $message->content;
}
