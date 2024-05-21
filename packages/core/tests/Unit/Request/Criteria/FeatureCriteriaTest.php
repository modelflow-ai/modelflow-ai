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

namespace ModelflowAi\Core\Tests\Unit\Request\Criteria;

use ModelflowAi\Core\Request\Criteria\FeatureCriteria;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\DecisionEnum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FeatureCriteriaTest extends TestCase
{
    use ProphecyTrait;

    public function testMatches(): void
    {
        $featureCriteria = FeatureCriteria::IMAGE_TO_TEXT;

        $this->assertSame(DecisionEnum::MATCH, $featureCriteria->matches(FeatureCriteria::IMAGE_TO_TEXT));
    }

    public function testMatchesReturnsFalseWhenCriteriaDoesNotMatch(): void
    {
        $featureCriteria = FeatureCriteria::IMAGE_TO_TEXT;

        $this->assertSame(DecisionEnum::SAME_TYPE, $featureCriteria->matches(FeatureCriteria::TOOLS));
    }

    public function testMatchesReturnsTrueForADifferentCriteria(): void
    {
        $mockCriteria = $this->prophesize(CriteriaInterface::class);

        $featureCriteria = FeatureCriteria::IMAGE_TO_TEXT;

        $this->assertSame(DecisionEnum::ABSTAIN, $featureCriteria->matches($mockCriteria->reveal()));
    }
}
