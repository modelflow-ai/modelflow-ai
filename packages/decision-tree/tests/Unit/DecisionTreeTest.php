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
use ModelflowAi\DecisionTree\DecisionRuleInterface;
use ModelflowAi\DecisionTree\DecisionTree;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DecisionTreeTest extends TestCase
{
    use ProphecyTrait;

    public function testDetermineAdapter(): void
    {
        $adapter = $this->prophesize(\stdClass::class);
        $rule = $this->prophesize(DecisionRuleInterface::class);
        $rule->getAdapter()->willReturn($adapter->reveal());
        $request = $this->prophesize(CriteriaBehaviour::class);
        $rule->matches($request->reveal())->willReturn(true);

        $decisionTree = new DecisionTree([$rule->reveal()]);

        $this->assertSame($adapter->reveal(), $decisionTree->determineAdapter($request->reveal()));
    }

    public function testDetermineAdapterThrowsExceptionWhenNoMatchingRule(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No matching adapter found.');

        $rule = $this->prophesize(DecisionRuleInterface::class);
        $request = $this->prophesize(CriteriaBehaviour::class);
        $rule->matches($request->reveal())->willReturn(false);

        $decisionTree = new DecisionTree([$rule->reveal()]);
        $decisionTree->determineAdapter($request->reveal());
    }
}
