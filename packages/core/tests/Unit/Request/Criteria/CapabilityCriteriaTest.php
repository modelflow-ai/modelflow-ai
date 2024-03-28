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

use ModelflowAi\Core\DecisionTree\DecisionEnum;
use ModelflowAi\Core\Request\Criteria\AiCriteriaInterface;
use ModelflowAi\Core\Request\Criteria\CapabilityCriteria;
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
        $mockCriteria = $this->prophesize(AiCriteriaInterface::class);

        $capabilityRequirement = CapabilityCriteria::SMART;

        $this->assertSame(DecisionEnum::ABSTAIN, $capabilityRequirement->matches($mockCriteria->reveal()));
    }
}
