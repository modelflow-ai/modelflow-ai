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
use ModelflowAi\Chat\Response\AIChatResponseMessage;
use ModelflowAi\DecisionTree\Criteria\PrivacyCriteria;
use ModelflowAi\PromptTemplate\ChatPromptTemplate;

/** @var AIChatRequestHandlerInterface $handler */
/** @var FakeChatAdapter $adapter */
[$adapter, $handler] = require_once __DIR__ . '/bootstrap.php';

$adapter->addMessage([
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'LEAVE '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'ME '),
    new AIChatResponseMessage(AIChatMessageRoleEnum::SYSTEM, 'ALONE'),
]);

$response = $handler->createStreamedRequest(
    ...ChatPromptTemplate::create(
        new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, 'You are an {feeling} bot'),
        new AIChatMessage(AIChatMessageRoleEnum::USER, 'Hello {where}!'),
    )->format(['where' => 'world', 'feeling' => 'angry']),
)
    ->addCriteria(PrivacyCriteria::HIGH)
    ->execute();

foreach ($response->getMessageStream() as $index => $message) {
    if (0 === $index) {
        echo $message->role->value . ': ';
    }

    echo $message->content;
}
