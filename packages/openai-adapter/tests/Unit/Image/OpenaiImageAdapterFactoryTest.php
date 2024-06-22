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

namespace ModelflowAi\OpenaiAdapter\Tests\Unit\Image;

use ModelflowAi\OpenaiAdapter\Image\OpenaiImageAdapterFactory;
use ModelflowAi\OpenaiAdapter\Image\OpenAIImageGenerationAdapter;
use OpenAI\Contracts\ClientContract;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenaiImageAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $httpClient = $this->prophesize(HttpClientInterface::class);
        $client = $this->prophesize(ClientContract::class);

        $factory = new OpenaiImageAdapterFactory($httpClient->reveal(), $client->reveal());

        $adapter = $factory->createImageAdapter([
            'model' => 'dall-e-3',
        ]);
        $this->assertInstanceOf(OpenAIImageGenerationAdapter::class, $adapter);
    }
}
