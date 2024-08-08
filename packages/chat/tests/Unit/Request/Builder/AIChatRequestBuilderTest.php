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

namespace ModelflowAi\Chat\Tests\Unit\Request\Builder;

use ModelflowAi\Chat\Request\AIChatRequest;
use ModelflowAi\Chat\Request\Builder\AIChatRequestBuilder;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\Chat\ToolInfo\ToolChoiceEnum;
use ModelflowAi\Chat\ToolInfo\ToolInfo;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\DecisionTree\Criteria\FeatureCriteria;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AIChatRequestBuilderTest extends TestCase
{
    use ProphecyTrait;

    public function testAddOptions(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->addOptions(['format' => 'json']);

        $this->assertSame('json', $builder->build()->getOption('format'));
    }

    public function testAsJson(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->asJson();

        $this->assertSame('json', $builder->build()->getOption('format'));
    }

    public function testStreamed(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->streamed();

        $this->assertTrue($builder->build()->getOption('streamed'));
    }

    public function testAddCriteria(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->addCriteria(FeatureCriteria::IMAGE_TO_TEXT);

        $this->assertTrue($builder->build()->matches([FeatureCriteria::IMAGE_TO_TEXT]));
        $this->assertFalse($builder->build()->matches([FeatureCriteria::TOOLS]));
    }

    public function testAddCriteriaArray(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->addCriteria([
            FeatureCriteria::IMAGE_TO_TEXT,
            CapabilityCriteria::SMART,
        ]);

        $this->assertTrue($builder->build()->matches([FeatureCriteria::IMAGE_TO_TEXT]));
        $this->assertTrue($builder->build()->matches([CapabilityCriteria::SMART]));
        $this->assertFalse($builder->build()->matches([FeatureCriteria::TOOLS]));
    }

    public function testAddMessage(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);
        $message = new AIChatMessage(AIChatMessageRoleEnum::USER, 'test message');

        $builder->addMessage($message);

        $this->assertCount(1, $builder->build()->getMessages());
    }

    public function testAddMessages(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);
        $messages = [
            new AIChatMessage(AIChatMessageRoleEnum::USER, 'test message'),
            new AIChatMessage(AIChatMessageRoleEnum::SYSTEM, 'test message'),
        ];

        $builder->addMessages($messages);

        $this->assertCount(2, $builder->build()->getMessages());
    }

    public function testAddSystemMessages(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->addSystemMessage('test message');

        /** @var AIChatMessage[] $messages */
        $messages = $builder->build()->getMessages();
        $this->assertCount(1, $messages);
        $this->assertSame(AIChatMessageRoleEnum::SYSTEM, $messages[0]->role);
    }

    public function testAddAssistantMessages(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->addAssistantMessage('test message');

        /** @var AIChatMessage[] $messages */
        $messages = $builder->build()->getMessages();
        $this->assertCount(1, $messages);
        $this->assertSame(AIChatMessageRoleEnum::ASSISTANT, $messages[0]->role);
    }

    public function testAddUserMessages(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->addUserMessage('test message');

        /** @var AIChatMessage[] $messages */
        $messages = $builder->build()->getMessages();
        $this->assertCount(1, $messages);
        $this->assertSame(AIChatMessageRoleEnum::USER, $messages[0]->role);
    }

    public function testTooChoice(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->toolChoice(ToolChoiceEnum::AUTO);

        $this->assertSame(ToolChoiceEnum::AUTO, $builder->build()->getOption('toolChoice'));
    }

    public function testAddTool(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);

        $builder->tool('test', $this, 'toolMethod');

        $tools = $builder->build()->getTools();
        $this->assertCount(1, $tools);
        $this->assertSame($this, $tools['test'][0]);
        $this->assertSame('toolMethod', $tools['test'][1]);

        $toolInfos = $builder->build()->getToolInfos();
        $this->assertCount(1, $toolInfos);
        $this->assertInstanceOf(ToolInfo::class, $toolInfos[0]);
        $this->assertSame('test', $toolInfos[0]->name);
    }

    public function testBuild(): void
    {
        $builder = new AIChatRequestBuilder(fn () => null);
        $message = new AIChatMessage(AIChatMessageRoleEnum::USER, 'test message');

        $builder->addMessage($message);

        $this->assertInstanceOf(
            AIChatRequest::class,
            $builder->build(),
        );
    }

    public function toolMethod(string $test): string
    {
        return $test;
    }
}
