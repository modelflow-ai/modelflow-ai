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

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/ProviderCriteria.php';

use ModelflowAi\Core\AIRequestHandler;
use ModelflowAi\Core\Model\AIModelAdapterInterface;
use ModelflowAi\Core\Request\AIRequestInterface;
use ModelflowAi\Core\Request\Criteria\CapabilityCriteria;
use ModelflowAi\Core\Request\Criteria\PrivacyCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\Mistral\Mistral;
use ModelflowAi\Mistral\Model;
use ModelflowAi\MistralAdapter\Model\MistralChatModelAdapter;
use ModelflowAi\Ollama\Ollama;
use ModelflowAi\OllamaAdapter\Model\OllamaChatModelAdapter;
use ModelflowAi\OllamaAdapter\Model\OllamaCompletionModelAdapter;
use ModelflowAi\OpenaiAdapter\Model\OpenaiChatModelAdapter;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$adapter = [];

$mistralApiKey = $_ENV['MISTRAL_API_KEY'];
if ($mistralApiKey) {
    $mistralClient = Mistral::client($mistralApiKey);
    $mistralChatAdapter = new MistralChatModelAdapter($mistralClient, Model::LARGE);

    /** @var DecisionRule<AIRequestInterface, AIModelAdapterInterface> $rule */
    $rule = new DecisionRule($mistralChatAdapter, [ProviderCriteria::MISTRAL, PrivacyCriteria::MEDIUM]);
    $adapter[] = $rule;
}

$openaiApiKey = $_ENV['OPENAI_API_KEY'];
if ($openaiApiKey) {
    $openAiClient = \OpenAI::client($openaiApiKey);
    $gpt4Adapter = new OpenaiChatModelAdapter($openAiClient, 'gpt-3.5-turbo-0125');

    /** @var DecisionRule<AIRequestInterface, AIModelAdapterInterface> $rule */
    $rule = new DecisionRule($gpt4Adapter, [ProviderCriteria::OPENAI, PrivacyCriteria::LOW, CapabilityCriteria::SMART]);
    $adapter[] = $rule;
}

$client = Ollama::client();
$llama2ChatAdapter = new OllamaChatModelAdapter($client);
$llama2TextAdapter = new OllamaCompletionModelAdapter($client);

/** @var DecisionRule<AIRequestInterface, AIModelAdapterInterface> $rule */
$rule = new DecisionRule($llama2TextAdapter, [ProviderCriteria::OLLAMA, PrivacyCriteria::HIGH]);
$adapter[] = $rule;

/** @var DecisionRule<AIRequestInterface, AIModelAdapterInterface> $rule */
$rule = new DecisionRule($llama2ChatAdapter, [ProviderCriteria::OLLAMA, PrivacyCriteria::HIGH]);
$adapter[] = $rule;

/** @var DecisionTreeInterface<AIRequestInterface, AIModelAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree($adapter);

return new AIRequestHandler($decisionTree);
