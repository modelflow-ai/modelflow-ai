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

use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\AIChatRequestHandler;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\Criteria\PrivacyCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\Mistral\Mistral;
use ModelflowAi\Mistral\Model;
use ModelflowAi\MistralAdapter\Chat\MistralChatAdapter;
use ModelflowAi\Ollama\Ollama;
use ModelflowAi\OllamaAdapter\Chat\OllamaChatAdapter;
use ModelflowAi\OpenaiAdapter\Chat\OpenaiChatAdapter;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$adapter = [];

$mistralApiKey = $_ENV['MISTRAL_API_KEY'];
if ($mistralApiKey) {
    $mistralClient = Mistral::client($mistralApiKey);
    $mistralChatAdapter = new MistralChatAdapter($mistralClient, Model::LARGE);

    /** @var DecisionRule<AIChatRequest, AIChatAdapterInterface> $rule */
    $rule = new DecisionRule($mistralChatAdapter, [ProviderCriteria::MISTRAL, PrivacyCriteria::MEDIUM]);
    $adapter[] = $rule;
}

$openaiApiKey = $_ENV['OPENAI_API_KEY'];
if ($openaiApiKey) {
    $openAiClient = \OpenAI::client($openaiApiKey);
    $gpt4Adapter = new OpenaiChatAdapter($openAiClient, 'gpt-3.5-turbo-0125');

    /** @var DecisionRule<AIChatRequest, AIChatAdapterInterface> $rule */
    $rule = new DecisionRule($gpt4Adapter, [ProviderCriteria::OPENAI, PrivacyCriteria::LOW, CapabilityCriteria::SMART]);
    $adapter[] = $rule;
}

$client = Ollama::client();
$llama2ChatAdapter = new OllamaChatAdapter($client);

/** @var DecisionRule<AIChatRequest, AIChatAdapterInterface> $rule */
$rule = new DecisionRule($llama2ChatAdapter, [ProviderCriteria::OLLAMA, PrivacyCriteria::HIGH]);
$adapter[] = $rule;

/** @var DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree($adapter);

return new AIChatRequestHandler($decisionTree);
