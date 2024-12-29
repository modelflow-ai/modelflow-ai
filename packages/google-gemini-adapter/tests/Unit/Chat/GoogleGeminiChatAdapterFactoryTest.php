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

namespace ModelflowAi\GoogleGeminiAdapter\Tests\Unit\Chat;

use Gemini\Contracts\ClientContract;
use Gemini\Enums\ModelType;
use ModelflowAi\GoogleGeminiAdapter\Chat\GoogleGeminiChatAdapter;
use ModelflowAi\GoogleGeminiAdapter\Chat\GoogleGeminiChatAdapterFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class GoogleGeminiChatAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $client = $this->prophesize(ClientContract::class);

        $factory = new GoogleGeminiChatAdapterFactory($client->reveal());

        $adapter = $factory->createChatAdapter([
            'model' => ModelType::GEMINI_FLASH->value,
        ]);
        $this->assertInstanceOf(GoogleGeminiChatAdapter::class, $adapter);
    }
}
