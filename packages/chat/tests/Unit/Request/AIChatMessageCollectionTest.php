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

namespace ModelflowAi\Chat\Tests\Unit\Request;

use ModelflowAi\Chat\Request\AIChatMessageCollection;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\Request\ResponseFormat\ResponseFormatInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AIChatMessageCollectionTest extends TestCase
{
    use ProphecyTrait;

    public function testToArray(): void
    {
        $message1 = new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, 'Test content 1');
        $message2 = new AIChatMessage(AIChatMessageRoleEnum::USER, 'Test content 2');

        $collection = new AIChatMessageCollection($message1, $message2);

        $expected = [
            [
                'role' => 'assistant',
                'content' => 'Test content 1',
            ],
            [
                'role' => 'user',
                'content' => 'Test content 2',
            ],
        ];

        $this->assertSame($expected, $collection->toArray());
    }

    public function testAddResponseFormat(): void
    {
        $formatMock = $this->prophesize(ResponseFormatInterface::class);
        $formatMock->asString()->willReturn('mocked-schema');

        $collection = new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, 'Existing system message'),
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'Hello'),
        );

        $this->assertCount(2, $collection, 'Collection should have two messages to start.');
        $collection->addResponseFormat($formatMock->reveal());

        $this->assertCount(3, $collection, 'A system message should be inserted before the first non-system message.');
        $this->assertSame(AIChatMessageRoleEnum::SYSTEM, $collection[1]?->role, 'The newly added system message should appear at index 1.');
        $this->assertSame('mocked-schema', $collection[1]->toArray()['content'], 'Ensure the new system message contains the correct content.');
    }

    public function testAddResponseFormatOnEmptyCollection(): void
    {
        $formatMock = $this->prophesize(ResponseFormatInterface::class);
        $formatMock->asString()->willReturn('{"type":"object","properties":{"dummy":"string"}}');

        $collection = new AIChatMessageCollection();
        $this->assertCount(0, $collection, 'Initial collection should be empty.');

        $collection->addResponseFormat($formatMock->reveal());
        $this->assertCount(1, $collection, 'The collection should now contain one message.');
        $this->assertSame(AIChatMessageRoleEnum::SYSTEM, $collection[0]?->role);
        $this->assertSame('{"type":"object","properties":{"dummy":"string"}}', $collection[0]->toArray()['content']);
    }

    public function testAddResponseFormatInsertsSystemMessageBeforeNonSystemMessage(): void
    {
        $formatMock = $this->prophesize(ResponseFormatInterface::class);
        $formatMock->asString()->willReturn('mocked-schema');

        $collection = new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'Hello'),
            new AIChatMessage(AIChatMessageRoleEnum::ASSISTANT, 'Hi!'),
        );

        $this->assertCount(2, $collection, 'Ensure the collection initially has two messages.');
        $collection->addResponseFormat($formatMock->reveal());

        $this->assertCount(3, $collection, 'Ensure one new system message was inserted.');
        $this->assertSame(AIChatMessageRoleEnum::SYSTEM, $collection[0]?->role, 'System message should be inserted before the first non-system message.');
        $this->assertSame('mocked-schema', $collection[0]->toArray()['content']);
        $this->assertSame('Hello', $collection[1]?->toArray()['content'], 'User message should be second after insertion.');
        $this->assertSame('Hi!', $collection[2]?->toArray()['content']);
    }

    public function testAddResponseFormatThrowsExceptionWhenAlreadySet(): void
    {
        $formatMock = $this->prophesize(ResponseFormatInterface::class);
        $formatMock->asString()->willReturn('mocked-schema');

        $collection = new AIChatMessageCollection(
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'Hello'),
        );

        $collection->addResponseFormat($formatMock->reveal());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Response format already set.');
        $collection->addResponseFormat($formatMock->reveal());
    }
}
