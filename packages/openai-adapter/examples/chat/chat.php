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
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\PromptTemplate\ChatPromptTemplate;

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$response = $handler->createRequest(
    ...ChatPromptTemplate::create(
        new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, 'You are an {feeling} bot'),
        new AIChatMessage(AIChatMessageRoleEnum::USER, 'Hello {where}!'),
    )->format(['where' => 'world', 'feeling' => 'angry']),
)
    ->addCriteria(CapabilityCriteria::BASIC)
    ->execute();

echo \sprintf('%s: %s', $response->getMessage()->role->value, $response->getMessage()->content);
