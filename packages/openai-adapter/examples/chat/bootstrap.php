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

$openaiClient = require_once \dirname(__DIR__) . '/bootstrap.php';

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\AIChatRequestHandler;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\OpenaiAdapter\Chat\OpenaiChatAdapter;

$adapter = [];

$gpt4Adapter = new OpenaiChatAdapter($openaiClient, 'gpt-4');
$gpt35Adapter = new OpenaiChatAdapter($openaiClient, 'gpt-3.5');

$adapter[] = new DecisionRule($gpt4Adapter, [CapabilityCriteria::SMART]);
$adapter[] = new DecisionRule($gpt35Adapter, [CapabilityCriteria::BASIC]);

/** @var DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree($adapter);

return new AIChatRequestHandler($decisionTree);
