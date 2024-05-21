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

use ModelflowAi\Core\Request\Criteria\FlagCriteriaTrait;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;

enum ProviderCriteria: string implements CriteriaInterface
{
    use FlagCriteriaTrait;

    case OLLAMA = 'ollama';
    case OPENAI = 'openai';
    case MISTRAL = 'mistral';
}
