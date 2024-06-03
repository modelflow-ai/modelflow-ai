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

namespace ModelflowAi\DecisionTree\Tests\Unit\Criteria;

use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\DecisionEnum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CapabilityCriteriaTest extends TestCase
{
    use ProphecyTrait;

    public function testMatches(): void
    {
        $capabilityRequirement = CapabilityCriteria::BASIC;

        $this->assertSame(DecisionEnum::MATCH, $capabilityRequirement->matches(CapabilityCriteria::SMART));
    }

    public function testMatchesReturnsFalseWhenCriteriaDoesNotMatch(): void
    {
        $capabilityRequirement = CapabilityCriteria::SMART;

        $this->assertSame(DecisionEnum::NO_MATCH, $capabilityRequirement->matches(CapabilityCriteria::BASIC));
    }

    public function testMatchesReturnsTrueForADifferentCriteria(): void
    {
        $mockCriteria = $this->prophesize(CriteriaInterface::class);

        $capabilityRequirement = CapabilityCriteria::SMART;

        $this->assertSame(DecisionEnum::ABSTAIN, $capabilityRequirement->matches($mockCriteria->reveal()));
    }
}
