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

namespace ModelflowAi\MistralAdapter\Tests\Unit;

use ModelflowAi\Mistral\ClientInterface;
use ModelflowAi\Mistral\Model;
use ModelflowAi\MistralAdapter\MistralChatAdapterFactory;
use ModelflowAi\MistralAdapter\Model\MistralChatModelAdapter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MistralChatAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $factory = new MistralChatAdapterFactory($client->reveal());

        $adapter = $factory->createChatAdapter([
            'model' => Model::MEDIUM->value,
            'image_to_text' => true,
            'functions' => true,
            'priority' => 0,
        ]);
        $this->assertInstanceOf(MistralChatModelAdapter::class, $adapter);
    }
}
