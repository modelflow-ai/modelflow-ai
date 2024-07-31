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

use ModelflowAi\Completion\AICompletionRequestHandlerInterface;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\PromptTemplate\PromptTemplate;

/** @var AICompletionRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$response = $handler->createRequest(
    PromptTemplate::create('You are an {feeling} bot!' . \PHP_EOL . 'Hello {where}!')
        ->format(['where' => 'world', 'feeling' => 'angry']),
)
    ->addCriteria(CapabilityCriteria::BASIC)
    ->build()
    ->execute();

echo $response->getContent();
