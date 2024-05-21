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

namespace ModelflowAi\DecisionTree\Tests\Unit;

use ModelflowAi\DecisionTree\Behaviour\CriteriaBehaviour;
use ModelflowAi\DecisionTree\Behaviour\SupportsBehaviour;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\DecisionEnum;
use ModelflowAi\DecisionTree\DecisionRule;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class DecisionRuleTest extends TestCase
{
    use ProphecyTrait;

    public function testMatches(): void
    {
        $adapter = $this->prophesize(SupportsBehaviour::class);
        $criteria = $this->prophesize(CriteriaInterface::class);
        $request = $this->prophesize(CriteriaBehaviour::class);
        $request->matches([$criteria->reveal()])->willReturn(true);
        $adapter->supports($request->reveal())->willReturn(true);

        $decisionRule = new DecisionRule($adapter->reveal(), [$criteria->reveal()]);

        $this->assertTrue($decisionRule->matches($request->reveal()));
    }

    public function testMatchesReturnsFalseWhenCriteriaDoesNotMatch(): void
    {
        $adapter = $this->prophesize(SupportsBehaviour::class);
        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->matches(Argument::any())->willReturn(DecisionEnum::NO_MATCH);
        $request = $this->prophesize(CriteriaBehaviour::class);
        $request->matches([$criteria->reveal()])->willReturn(false);
        $adapter->supports($request->reveal())->willReturn(true);

        $decisionRule = new DecisionRule($adapter->reveal(), [$criteria->reveal()]);

        $this->assertFalse($decisionRule->matches($request->reveal()));
    }

    public function testGetAdapter(): void
    {
        $adapter = $this->prophesize(SupportsBehaviour::class);
        $decisionRule = new DecisionRule($adapter->reveal(), []);

        $this->assertSame($adapter->reveal(), $decisionRule->getAdapter());
    }
}
