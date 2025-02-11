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

namespace ModelflowAi\Integration\Symfony\Criteria;

use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\Criteria\FlagCriteriaTrait;

enum ModelCriteria: string implements CriteriaInterface
{
    use FlagCriteriaTrait;

    // Ollama
    case LLAMA2 = 'llama2';
    case LLAMA3 = 'llama3';
    case LLAMA3_2 = 'llama3.2';
    case NEXUSRAVEN = 'nexusraven';
    case LLAVA = 'llava';

    // OpenAI
    case GPT4O = 'gpt4o';
    case GPT4O_MINI = 'gpt4o-mini';
    case GPT4 = 'gpt4';
    case GPT3_5 = 'gpt3.5-turbo';
    case DALL_E_3 = 'dall-e-3';
    case DALL_E_2 = 'dall-e-2';

    // Mistral
    case MISTRAL_TINY = 'mistral-tiny';
    case MISTRAL_SMALL = 'mistral-small-latest';
    case MISTRAL_MEDIUM = 'mistral-medium-latest';
    case MISTRAL_LARGE = 'mistral-large-latest';
    case MISTRAL_NEMO = 'open-mistral-nemo';
    case PIXTRAL_LARGE = 'pixtral-large-latest';

    // Anthropic
    case CLAUDE_3_OPUS = 'claude-3-opus-20240229';
    case CLAUDE_3_5_SONNET = 'claude-3-5-sonnet-20241022';
    case CLAUDE_3_SONNET = 'claude-3-sonnet-20240229';
    case CLAUDE_3_5_HAIKU = 'claude-3-5-haiku-20241022';
    case CLAUDE_3_HAIKU = 'claude-3-haiku-20240307';

    // FireworksAI
    case LLAMA3_1_405B_FIREWORKS = 'accounts/fireworks/models/llama-v3p1-405b-instruct';
    case LLAMA3_1_70B_FIREWORKS = 'accounts/fireworks/models/llama-v3p1-70b-instruct';
    case LLAMA3_1_8B_FIREWORKS = 'accounts/fireworks/models/llama-v3p1-8b-instruct';
    case LLAMA3_70B_FIREWORKS = 'accounts/fireworks/models/llama-v3-70b-instruct';
    case FIREFUNCTION_V2_FIREWORKS = 'accounts/fireworks/models/firefunction-v2';
    case MIXTRAL_FIREWORKS = 'accounts/fireworks/models/mixtral-8x22b-instruct';
    case LLAVA_13B_FIREWORKS = 'accounts/fireworks/models/firellava-13b';
    case STABLE_DIFFUSSION_XL_1024_FIREWORKS = 'stable-diffusion-xl-1024-v1-0';

    // Google Gemini
    case GEMINI_1_5_PRO = 'gemini-pro';
    case GEMINI_1_5_FLASH = 'gemini-1.5-flash';
}
