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

namespace ModelflowAi\FireworksAiAdapter\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Psr18ClientDecorator implements ClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->client->sendRequest($request);

        // Add the x-request-id header to the response
        // This is a workaround for the missing x-request-id header in the response
        // The class vendor/openai-php/client/src/Responses/Meta/MetaInformation.php needs this header
        // to create the MetaInformation object from the response headers
        $response = $response->withHeader('x-request-id', ['']);

        // Return a StreamDecorator object instead of the original response body
        // This is a workaround for the missing values in the response body
        return $response->withBody(new StreamDecorator($response->getBody()));
    }
}
