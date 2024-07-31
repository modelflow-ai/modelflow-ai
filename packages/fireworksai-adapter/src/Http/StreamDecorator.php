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

use Psr\Http\Message\StreamInterface;

/**
 * This class is a workaround for the missing values in the response body.
 *
 * Cases:
 * - CreateStreamedResponseToolCallFunction expects an arguments key in the response body but it is missing when no arguments will be called.
 *   - Example: data: {"id":"186107ef-9d02-4dfd-86b9-a3e57a878424","object":"chat.completion.chunk","created":1721886402,"model":"accounts/fireworks/models/firefunction-v2","choices":[{"index":0,"delta":{"tool_calls":[{"index":0,"id":"call_87ZGLUzU4LdOylKECYpoo5eD","type":"function","function":{"name":"get_current_weather"}}]},"finish_reason":null}],"usage":null}
 */
class StreamDecorator implements StreamInterface
{
    private string $lineBuffer = '';

    /**
     * @var string[]
     */
    private array $nextCharacters = [];

    public function __construct(
        private readonly StreamInterface $stream,
    ) {
    }

    public function __toString(): string
    {
        return $this->stream->__toString();
    }

    public function close(): void
    {
        $this->stream->close();
    }

    public function detach()
    {
        return $this->stream->detach();
    }

    public function getSize(): ?int
    {
        return $this->stream->getSize();
    }

    public function tell(): int
    {
        return $this->stream->tell();
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function isSeekable(): bool
    {
        return $this->stream->isSeekable();
    }

    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        $this->stream->seek($offset, $whence);
    }

    public function rewind(): void
    {
        $this->stream->rewind();
    }

    public function isWritable(): bool
    {
        return $this->stream->isWritable();
    }

    public function write(string $string): int
    {
        return $this->stream->write($string);
    }

    public function isReadable(): bool
    {
        return $this->stream->isReadable();
    }

    public function read(int $length): string
    {
        if ([] !== $this->nextCharacters) {
            $buffer = \array_splice($this->nextCharacters, 0, $length);

            return \implode('', $buffer);
        }

        $buffer = $this->stream->read($length);
        $this->lineBuffer .= $buffer;

        if (\preg_match('/"function":{"name":"[^"]+"$/', $this->lineBuffer)) {
            $next = $this->stream->read(1);
            $this->nextCharacters = ',' === $next ? [$next] : \str_split(',"arguments":""' . $next);
        }

        if (\str_ends_with($buffer, "\n")) {
            $this->lineBuffer = '';
        }

        return $buffer;
    }

    public function getContents(): string
    {
        return $this->stream->getContents();
    }

    public function getMetadata(?string $key = null)
    {
        return $this->stream->getMetadata($key);
    }
}
