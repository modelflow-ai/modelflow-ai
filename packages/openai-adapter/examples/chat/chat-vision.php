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
use ModelflowAi\Chat\Request\Message\ImageBase64Part;
use ModelflowAi\Chat\Request\Message\TextPart;

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$response = $handler->createRequest(
    new AIChatMessage(AIChatMessageRoleEnum::USER, [
        TextPart::create('What is on the image?'),
        ImageBase64Part::create(__DIR__ . '/image.jpg'),
    ]),
)
    ->execute();

// Display full response details
$message = $response->getMessage();
echo "Role: {$message->role->value}\n";
echo "Content: {$message->content}\n";

// Display usage statistics
$usage = $response->getUsage();
echo "\nToken Usage:\n";
echo "Input tokens: {$usage?->inputTokens}\n";
echo "Output tokens: {$usage?->outputTokens}\n";
echo "Total tokens: {$usage?->totalTokens}\n";
