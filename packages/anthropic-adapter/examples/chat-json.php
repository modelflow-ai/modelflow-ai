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

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$response = $handler->createRequest(
    new AIChatMessage(AIChatMessageRoleEnum::USER, 'You are a BOT that help me to generate ideas for my project.'),
)
    ->asJson()
    ->addCriteria(CapabilityCriteria::BASIC)
    ->execute();

$content = \json_decode($response->getMessage()->content, true, 512, \JSON_THROW_ON_ERROR);

echo \json_encode($content, \JSON_PRETTY_PRINT);
