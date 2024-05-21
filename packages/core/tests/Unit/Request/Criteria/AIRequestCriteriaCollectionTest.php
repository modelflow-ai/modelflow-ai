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

use ModelflowAi\Core\Request\Criteria\AIRequestCriteriaCollection;
use ModelflowAi\Core\Request\Criteria\CapabilityCriteria;
use ModelflowAi\Core\Request\Criteria\FeatureCriteria;
use ModelflowAi\DecisionTree\Criteria\CriteriaInterface;
use ModelflowAi\DecisionTree\DecisionEnum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AIRequestCriteriaCollectionTest extends TestCase
{
    use ProphecyTrait;

    public function testMatchesWithMocks(): void
    {
        $toMatch = $this->prophesize(CriteriaInterface::class);

        $criteria1 = $this->prophesize(CriteriaInterface::class);
        $criteria1->matches($toMatch->reveal())->willReturn(DecisionEnum::MATCH);
        $criteria2 = $this->prophesize(CriteriaInterface::class);
        $criteria2->matches($toMatch->reveal())->willReturn(DecisionEnum::MATCH);

        $criteriaCollection = new AIRequestCriteriaCollection([$criteria1->reveal(), $criteria2->reveal()]);

        $this->assertTrue($criteriaCollection->matches([$toMatch->reveal()]));
    }

    public function testMatchesReturnsFalseWhenCriteriaDoesNotMatch(): void
    {
        $toMatch = $this->prophesize(CriteriaInterface::class);

        $mockCriteria1 = $this->prophesize(CriteriaInterface::class);
        $mockCriteria1->matches($toMatch->reveal())->willReturn(DecisionEnum::MATCH);
        $mockCriteria2 = $this->prophesize(CriteriaInterface::class);
        $mockCriteria2->matches($toMatch->reveal())->willReturn(DecisionEnum::NO_MATCH);

        $criteriaCollection = new AIRequestCriteriaCollection([$mockCriteria1->reveal(), $mockCriteria2->reveal()]);

        $this->assertFalse($criteriaCollection->matches([$toMatch->reveal()]));
    }

    public function testWithFeatures(): void
    {
        $criteriaCollection = new AIRequestCriteriaCollection();
        $features = [FeatureCriteria::IMAGE_TO_TEXT];

        $newCriteriaCollection = $criteriaCollection->withFeatures($features);

        $this->assertTrue($newCriteriaCollection->matches([FeatureCriteria::IMAGE_TO_TEXT]));
    }

    /**
     * @return array<array{
     *     0: CriteriaInterface[],
     *     1: CriteriaInterface[],
     *     2: bool,
     * }>
     */
    public static function provideMatches(): array
    {
        return [
            [
                [FeatureCriteria::IMAGE_TO_TEXT],
                [FeatureCriteria::IMAGE_TO_TEXT, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT, CapabilityCriteria::SMART],
                [FeatureCriteria::IMAGE_TO_TEXT, CapabilityCriteria::ADVANCED],
                false,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT, CapabilityCriteria::ADVANCED],
                [FeatureCriteria::IMAGE_TO_TEXT, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT],
                [FeatureCriteria::STREAM],
                false,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT],
                [FeatureCriteria::IMAGE_TO_TEXT],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT],
                [FeatureCriteria::STREAM],
                false,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT, CapabilityCriteria::ADVANCED],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, FeatureCriteria::TOOLS],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                false,
            ],
            [
                [CapabilityCriteria::ADVANCED],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [CapabilityCriteria::BASIC],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                true,
            ],
            [
                [CapabilityCriteria::SMART],
                [FeatureCriteria::IMAGE_TO_TEXT, FeatureCriteria::STREAM, CapabilityCriteria::ADVANCED],
                false,
            ],
        ];
    }

    /**
     * @dataProvider provideMatches
     *
     * @param CriteriaInterface[] $requestCriteria
     * @param CriteriaInterface[] $ruleCriteria
     */
    public function testMatchesWithDifferentCombinations(
        array $requestCriteria,
        array $ruleCriteria,
        bool $expected,
    ): void {
        $criteriaCollection = new AIRequestCriteriaCollection($requestCriteria);

        $this->assertSame($expected, $criteriaCollection->matches($ruleCriteria));
    }
}
