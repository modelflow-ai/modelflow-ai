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

use Gemini\Enums\ModelType;
use ModelflowAi\Chat\Adapter\AIChatAdapterInterface;
use ModelflowAi\Chat\AIChatRequestHandler;
use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\DecisionTree\Criteria\FeatureCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\GoogleGeminiAdapter\Chat\GoogleGeminiChatAdapter;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$adapters = [];

$googleGeminiApiKey = $_ENV['GOOGLE_GEMINI_API_KEY'];
if (!$googleGeminiApiKey) {
    throw new \RuntimeException('Google Gemini API key is required');
}

$googleGeminiClient = \Gemini::client($googleGeminiApiKey);

$adapterPro = new GoogleGeminiChatAdapter($googleGeminiClient, ModelType::GEMINI_PRO->value);
$adapterProVision = new GoogleGeminiChatAdapter($googleGeminiClient, ModelType::GEMINI_FLASH->value);

$adapters[] = new DecisionRule($adapterPro, [FeatureCriteria::STREAM]);
$adapters[] = new DecisionRule($adapterProVision, [FeatureCriteria::STREAM, FeatureCriteria::IMAGE_TO_TEXT]);

/** @var DecisionTreeInterface<AIChatRequest, AIChatAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree($adapters);

return new AIChatRequestHandler($decisionTree);
