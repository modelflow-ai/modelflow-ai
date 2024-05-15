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

use ModelflowAi\Core\AIRequestHandler;
use ModelflowAi\Core\DecisionTree\AIModelDecisionTree;
use ModelflowAi\Core\DecisionTree\AIModelDecisionTreeInterface;
use ModelflowAi\Core\DecisionTree\DecisionRule;
use ModelflowAi\Core\Model\AIModelAdapterInterface;
use ModelflowAi\Core\Request\AIRequestInterface;
use ModelflowAi\Core\Request\Criteria\CapabilityCriteria;
use ModelflowAi\OpenaiAdapter\Model\OpenaiChatModelAdapter;

$adapter = [];

$gpt4Adapter = new OpenaiChatModelAdapter($openaiClient, 'gpt-4');
$gpt35Adapter = new OpenaiChatModelAdapter($openaiClient, 'gpt-3.5');

$adapter[] = new DecisionRule($gpt4Adapter, [CapabilityCriteria::SMART]);
$adapter[] = new DecisionRule($gpt35Adapter, [CapabilityCriteria::BASIC]);

/** @var AIModelDecisionTreeInterface<AIRequestInterface, AIModelAdapterInterface> $decisionTree */
$decisionTree = new AIModelDecisionTree($adapter);

return new AIRequestHandler($decisionTree);
