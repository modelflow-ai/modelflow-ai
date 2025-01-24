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
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ToolCallsPart;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolExecutor;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;

require_once __DIR__ . '/WeatherTool.php';

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$toolExecutor = new ToolExecutor();

$builder = $handler->createStreamedRequest()
    ->addUserMessage('How is the weather in hohenems and vienna?')
    ->tool('get_current_weather', new WeatherTool(), 'getCurrentWeather')
    ->toolChoice(ToolChoiceEnum::AUTO)
    ->addCriteria(CapabilityCriteria::SMART);

$response = $builder->execute();

foreach ($response->getMessageStream() as $message) {
    $toolCalls = $message->toolCalls;
    if (null !== $toolCalls && 0 < \count($toolCalls)) {
        $builder->addMessage(
            new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, ToolCallsPart::create($toolCalls)),
        );

        foreach ($toolCalls as $toolCall) {
            $builder->addMessage(
                $toolExecutor->execute($response->getRequest(), $toolCall),
            );
        }
    }
}

$response = $builder->execute();
foreach ($response->getMessageStream() as $index => $message) {
    if (0 === $index) {
        echo $message->role->value . ': ';
    }

    echo $message->content;
}
