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

namespace ModelflowAi\ApiClient\Transport;

use ModelflowAi\ApiClient\Exceptions\TransportException;
use ModelflowAi\ApiClient\Responses\MetaInformation;
use ModelflowAi\ApiClient\Transport\Response\ObjectResponse;
use ModelflowAi\ApiClient\Transport\Response\TextResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SymfonyHttpTransporter implements TransportInterface
{
    private readonly HttpClientInterface $client;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        HttpClientInterface $client,
        private readonly string $baseUrl,
        private readonly array $headers = [],
    ) {
        $this->client = $client->withOptions([
            'base_uri' => $this->baseUrl,
            'headers' => $this->headers,
        ]);
    }

    private function request(Payload $payload): ResponseInterface
    {
        return $this->client->request(
            $payload->method->value,
            $payload->resourceUri->__toString(),
            [
                'headers' => [
                    'Content-Type' => $payload->contentType->value,
                ],
                'json' => $payload->parameters,
            ],
        );
    }

    public function requestText(Payload $payload): TextResponse
    {
        $response = $this->request($payload);

        try {
            // @phpstan-ignore-next-line
            return new TextResponse($response->getContent(), MetaInformation::from($response->getHeaders()));
        } catch (ClientException $exception) {
            throw new TransportException($exception->getResponse(), $exception->getCode(), $exception);
        }
    }

    public function requestObject(Payload $payload): ObjectResponse
    {
        $response = $this->request($payload);

        try {
            // @phpstan-ignore-next-line
            return new ObjectResponse($response->toArray(), MetaInformation::from($response->getHeaders()));
        } catch (ClientException $exception) {
            throw new TransportException($exception->getResponse(), $exception->getCode(), $exception);
        }
    }

    public function requestStream(Payload $payload, ?callable $decoder = null): \Iterator
    {
        if (!$decoder) {
            $decoder = fn (ChunkInterface $chunk) => [\json_decode($chunk->getContent(), true)];
        }

        $response = $this->request($payload);

        try {
            // @phpstan-ignore-next-line
            $metaInformation = MetaInformation::from($response->getHeaders());
        } catch (ClientException $exception) {
            throw new TransportException($exception->getResponse(), $exception->getCode(), $exception);
        }

        foreach ($this->client->stream($response) as $chunk) {
            if ($chunk->isFirst() || $chunk->isLast()) {
                continue;
            }

            foreach ($decoder($chunk) as $data) {
                yield new ObjectResponse($data, $metaInformation);
            }
        }
    }
}
