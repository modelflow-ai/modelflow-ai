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

use ModelflowAi\Anthropic\Anthropic;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\AnthropicAdapter\Model\AnthropicChatModelAdapter;
use ModelflowAi\Core\AIRequestHandler;
use ModelflowAi\Core\Model\AIModelAdapterInterface;
use ModelflowAi\Core\Request\AIRequestInterface;
use ModelflowAi\Core\Request\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$adapter = [];

$anthropicApiKey = $_ENV['ANTHROPIC_API_KEY'];
if (!$anthropicApiKey) {
    throw new \RuntimeException('Anthropic API key is required');
}

$anthropicClient = Anthropic::client($anthropicApiKey);

$opusAdapter = new AnthropicChatModelAdapter($anthropicClient, Model::CLAUDE_3_OPUS);
$sonnetAdapter = new AnthropicChatModelAdapter($anthropicClient, Model::CLAUDE_3_SONNET);
$haikuAdapter = new AnthropicChatModelAdapter($anthropicClient, Model::CLAUDE_3_HAIKU);

$adapter[] = new DecisionRule($opusAdapter, [CapabilityCriteria::SMART]);
$adapter[] = new DecisionRule($sonnetAdapter, [CapabilityCriteria::INTERMEDIATE]);
$adapter[] = new DecisionRule($haikuAdapter, [CapabilityCriteria::BASIC]);

/** @var DecisionTreeInterface<AIRequestInterface, AIModelAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree($adapter);

return new AIRequestHandler($decisionTree);
