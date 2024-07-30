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

namespace ModelflowAi\FireworksAiAdapter\Tests\Unit\Image;

use ModelflowAi\FireworksAiAdapter\Image\FireworksAiImageAdapterFactory;
use ModelflowAi\FireworksAiAdapter\Image\FireworksAiImageGenerationAdapter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FireworksAiImageAdapterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateChatAdapter(): void
    {
        $httpClient = $this->prophesize(HttpClientInterface::class);
        $httpClient->withOptions([
            'base_uri' => 'https://api.fireworks.ai/inference/v1/image_generation/accounts/fireworks/models/',
            'headers' => [
                'Authorization' => 'Bearer 123-123-123',
            ],
        ])->willReturn($httpClient->reveal())->shouldBeCalled();

        $factory = new FireworksAiImageAdapterFactory($httpClient->reveal(), '123-123-123');

        $adapter = $factory->createImageAdapter([
            'model' => 'stable-diffusion-xl-1024-v1-0',
        ]);
        $this->assertInstanceOf(FireworksAiImageGenerationAdapter::class, $adapter);
    }
}
