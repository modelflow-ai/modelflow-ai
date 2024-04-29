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

namespace ModelflowAi\Anthropic;

use ModelflowAi\ApiClient\Transport\SymfonyHttpTransporter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Factory
{
    private HttpClientInterface $httpClient;

    private string $baseUrl = 'https://api.anthropic.com/v1/';

    private string $version = '2023-06-01';

    private ?string $beta = null;

    private ?string $apiKey = null;

    public function withHttpClient(HttpClientInterface $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    public function withBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function withApiKey(string $apiKey): self
    {
        $this->apiKey = \trim($apiKey);

        return $this;
    }

    public function withVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function withBeta(string $beta): self
    {
        $this->beta = $beta;

        return $this;
    }

    public function make(): ClientInterface
    {
        if (!$this->apiKey) {
            throw new \RuntimeException('API key is required to create a client');
        }

        $transporter = new SymfonyHttpTransporter($this->httpClient ?? HttpClient::create(), $this->baseUrl, \array_filter([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => $this->version,
            'anthropic-beta' => $this->beta,
            'content-type' => 'application/json',
        ]));

        return new Client($transporter);
    }
}
