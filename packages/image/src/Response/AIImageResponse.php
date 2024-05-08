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

namespace ModelflowAi\Image\Response;

use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;

class AIImageResponse
{
    /**
     * @param resource|string $resource
     */
    public function __construct(public AIImageRequest $request, public ImageFormat $imageFormat, public $resource)
    {
    }

    /**
     * @return resource
     */
    public function stream()
    {
        if (\is_resource($this->resource)) {
            return $this->resource;
        }

        /** @var resource $resource */
        $resource = \fopen('php://memory', 'w');
        \fwrite($resource, (string) \base64_decode($this->resource, true));
        \rewind($resource);

        return $resource;
    }

    public function base64(): string
    {
        if (\is_string($this->resource)) {
            return $this->resource;
        }

        $content = (string) \stream_get_contents($this->resource);

        return \base64_encode($content);
    }
}
