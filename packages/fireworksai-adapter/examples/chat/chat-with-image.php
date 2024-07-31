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
use ModelflowAi\Chat\Request\Message\ImageBase64Part;
use ModelflowAi\Chat\Request\Message\TextPart;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$response = $handler->createRequest(
    new AIChatMessage(AIChatMessageRoleEnum::USER, [TextPart::create('What is on this image!'), ImageBase64Part::create(__DIR__ . '/test_image.jpg')]),
)
    ->addCriteria(CapabilityCriteria::BASIC)
    ->build()
    ->execute();

echo \sprintf('%s: %s', $response->getMessage()->role->value, $response->getMessage()->content);
