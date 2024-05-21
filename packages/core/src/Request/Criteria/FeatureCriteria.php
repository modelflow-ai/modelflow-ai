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

namespace ModelflowAi\Core\Request\Criteria;

use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;

enum FeatureCriteria: string implements CriteriaInterface
{
    use FlagCriteriaTrait;

    case IMAGE_TO_TEXT = 'image_to_text';
    case TOOLS = 'tools';
    case STREAM = 'stream';
}
