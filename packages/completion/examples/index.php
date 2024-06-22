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

use ModelflowAi\Completion\Adapter\Fake\FakeCompletionAdapter;
use ModelflowAi\Completion\AICompletionRequestHandlerInterface;
use ModelflowAi\DecisionTree\Criteria\PrivacyCriteria;
use ModelflowAi\PromptTemplate\PromptTemplate;

/** @var AICompletionRequestHandlerInterface $handler */
/** @var FakeCompletionAdapter $adapter */
[$adapter, $handler] = require_once __DIR__ . '/bootstrap.php';

$adapter->addContent('Hey :)');

$response = $handler->createRequest(PromptTemplate::create('Hello {where}!')->format(['where' => 'world']))
    ->addCriteria(PrivacyCriteria::HIGH)
    ->build()
    ->execute();

echo $response->getContent();
