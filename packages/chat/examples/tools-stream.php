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

use ModelflowAi\Chat\Adapter\Fake\FakeChatAdapter;
use ModelflowAi\Chat\AIChatRequestHandlerInterface;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\Message\ToolCallsPart;
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\Chat\Response\AIChatToolCall;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolExecutor;
use ModelflowAi\Chat\ToolInfo\ToolTypeEnum;
use ModelflowAi\DecisionTree\Criteria\PrivacyCriteria;

require_once __DIR__ . '/WeatherTool.php';

/** @var AIChatRequestHandlerInterface $handler */
/** @var FakeChatAdapter $adapter */
[$adapter, $handler] = require_once __DIR__ . '/bootstrap.php';

$adapter->addMessage(new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, '', [
    new AIChatToolCall(ToolTypeEnum::FUNCTION, '123-123-123', 'get_current_weather', ['city' => 'hohenems']),
]));
$adapter->addMessage([
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'The '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'weather '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'in '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'hohenems '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'is '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'sunny'),
]);

$toolExecutor = new ToolExecutor();

$builder = $handler->createStreamedRequest()
    ->addUserMessage('How is the weather in hohenems and vienna?')
    ->tool('get_current_weather', new WeatherTool(), 'getCurrentWeather')
    ->toolChoice(ToolChoiceEnum::AUTO)
    ->addCriteria(PrivacyCriteria::HIGH);

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

$response = $builder->build()->execute();
foreach ($response->getMessageStream() as $index => $message) {
    if (0 === $index) {
        echo $message->role->value . ': ';
    }

    echo $message->content;
}
