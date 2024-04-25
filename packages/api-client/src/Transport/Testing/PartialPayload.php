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

namespace ModelflowAi\ApiClient\Transport\Testing;

use ModelflowAi\ApiClient\Transport\Enums\ContentType;
use ModelflowAi\ApiClient\Transport\Enums\Method;
use ModelflowAi\ApiClient\Transport\ValueObjects\ResourceUri;

class PartialPayload
{
    /**
     * @param array<string, mixed>|null $partialParameters
     */
    private function __construct(
        public ContentType $contentType,
        public Method $method,
        public ResourceUri $resourceUri,
        public ?array $partialParameters = [],
    ) {
    }

    /**
     * @param array<string, mixed> $partialParameters
     */
    public static function create(string $resource, array $partialParameters): self
    {
        return new self(
            contentType: ContentType::JSON,
            method: Method::POST,
            resourceUri: ResourceUri::get($resource),
            partialParameters: $partialParameters,
        );
    }
}
