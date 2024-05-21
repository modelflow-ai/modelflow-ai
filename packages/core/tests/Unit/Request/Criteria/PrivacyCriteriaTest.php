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

use ModelflowAi\Core\Request\Criteria\PrivacyCriteria;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\DecisionEnum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PrivacyCriteriaTest extends TestCase
{
    use ProphecyTrait;

    public function testMatches(): void
    {
        $privacyRequirement = PrivacyCriteria::LOW;

        $this->assertSame(DecisionEnum::MATCH, $privacyRequirement->matches(PrivacyCriteria::HIGH));
    }

    public function testMatchesReturnsFalseWhenCriteriaDoesNotMatch(): void
    {
        $privacyRequirement = PrivacyCriteria::HIGH;

        $this->assertSame(DecisionEnum::NO_MATCH, $privacyRequirement->matches(PrivacyCriteria::LOW));
    }

    public function testMatchesReturnsTrueForADifferentCriteria(): void
    {
        $mockCriteria = $this->prophesize(CriteriaInterface::class);

        $privacyRequirement = PrivacyCriteria::HIGH;

        $this->assertSame(DecisionEnum::ABSTAIN, $privacyRequirement->matches($mockCriteria->reveal()));
    }
}
