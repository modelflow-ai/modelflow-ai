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

namespace ModelflowAi\Experts;

use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\Experts\ResponseFormat\JsonSchemaResponseFormat;
use PHPUnit\Framework\TestCase;

class ExpertTest extends TestCase
{
    public function testConstruct(): void
    {
        $expert = new Expert(
            'name',
            'description',
            'instructions',
            [CapabilityCriteria::SMART],
        );

        $this->assertSame('name', $expert->getName());
        $this->assertSame('description', $expert->getDescription());
        $this->assertSame('instructions', $expert->getInstructions());
        $this->assertSame([CapabilityCriteria::SMART], $expert->getCriteria());
        $this->assertNull($expert->getResponseFormat());
    }

    public function testConstructWithResponseFormat(): void
    {
        $expert = new Expert(
            'name',
            'description',
            'instructions',
            [CapabilityCriteria::SMART],
            new JsonSchemaResponseFormat([]),
        );

        $this->assertSame('name', $expert->getName());
        $this->assertSame('description', $expert->getDescription());
        $this->assertSame('instructions', $expert->getInstructions());
        $this->assertSame([CapabilityCriteria::SMART], $expert->getCriteria());
        $this->assertNotNull($expert->getResponseFormat());
    }
}
