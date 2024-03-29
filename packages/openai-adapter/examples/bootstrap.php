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

require_once \dirname(__DIR__) . '/vendor/autoload.php';

use ModelflowAi\Core\AIRequestHandler;
use ModelflowAi\Core\DecisionTree\AIModelDecisionTree;
use ModelflowAi\Core\DecisionTree\DecisionRule;
use ModelflowAi\Core\Request\Criteria\CapabilityCriteria;
use ModelflowAi\OpenaiAdapter\Model\OpenaiChatModelAdapter;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$adapter = [];

$openaiApiKey = $_ENV['OPENAI_API_KEY'];
if (!$openaiApiKey) {
    throw new \RuntimeException('Openai API key is required');
}

$openaiClient = \OpenAI::client($openaiApiKey);

$gpt4Adapter = new OpenaiChatModelAdapter($openaiClient, 'gpt-4');
$gpt35Adapter = new OpenaiChatModelAdapter($openaiClient, 'gpt-3.5');

$adapter[] = new DecisionRule($gpt4Adapter, [CapabilityCriteria::SMART]);
$adapter[] = new DecisionRule($gpt35Adapter, [CapabilityCriteria::BASIC]);

$decisionTree = new AIModelDecisionTree($adapter);

return new AIRequestHandler($decisionTree);
