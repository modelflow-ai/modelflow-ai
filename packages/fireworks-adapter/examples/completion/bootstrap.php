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

$fireworksAiClient = require_once \dirname(__DIR__) . '/bootstrap.php';

use ModelflowAi\Completion\Adapter\AICompletionAdapterInterface;
use ModelflowAi\Completion\AICompletionRequestHandler;
use ModelflowAi\Completion\Request\AICompletionRequest;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\FireworksAiAdapter\Completion\FireworksAiCompletionAdapter;

$adapter = [];

$llama3Adapter = new FireworksAiCompletionAdapter($fireworksAiClient, 'accounts/fireworks/models/llama-v3-70b-instruct');
$firefunction2Adapter = new FireworksAiCompletionAdapter($fireworksAiClient, 'accounts/fireworks/models/firefunction-v2');
$llama31Adapter = new FireworksAiCompletionAdapter($fireworksAiClient, 'accounts/fireworks/models/llama-v3p1-405b-instruct');

$adapter[] = new DecisionRule($llama3Adapter, [CapabilityCriteria::BASIC]);
$adapter[] = new DecisionRule($firefunction2Adapter, [CapabilityCriteria::ADVANCED]);
$adapter[] = new DecisionRule($llama31Adapter, [CapabilityCriteria::SMART]);

/** @var DecisionTreeInterface<AICompletionRequest, AICompletionAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree($adapter);

return new AICompletionRequestHandler($decisionTree);
