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
$adapter->addMessage(new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'The weather in hohenems is sunny'));

$toolExecutor = new ToolExecutor();

$builder = $handler->createRequest()
    ->addUserMessage('How is the weather in hohenems?')
    ->tool('get_current_weather', new WeatherTool(), 'getCurrentWeather')
    ->toolChoice(ToolChoiceEnum::AUTO)
    ->addCriteria(PrivacyCriteria::HIGH);

$response = $builder->execute();

do {
    $toolCalls = $response->getMessage()->toolCalls;
    if (null !== $toolCalls && 0 < \count($toolCalls)) {
        $builder->addMessage(
            new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, ToolCallsPart::create($toolCalls)),
        );

        foreach ($toolCalls as $toolCall) {
            $builder->addMessage(
                $toolExecutor->execute($response->getRequest(), $toolCall),
            );
        }

        $response = $builder->build()->execute();
    }
} while (null !== $toolCalls && [] !== $toolCalls);

echo $response->getMessage()->role->value . ': ' . $response->getMessage()->content;
