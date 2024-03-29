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

namespace ModelflowAi\Embeddings\Model;

trait EmbeddingTrait
{
    public static function fromArray(array $data): self
    {
        $class = new \ReflectionClass(static::class);
        $instance = $class->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            if (!$class->hasProperty($key)) {
                continue;
            }

            $property = $class->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        }

        return $instance;
    }

    protected string $content;

    protected ?string $formattedContent = null;

    /**
     * @var float[]|null
     */
    protected ?array $vector = null;

    protected string $hash;

    protected int $chunkNumber = 0;

    public function split(string $content, int $chunkNumber): EmbeddingInterface
    {
        $embedding = clone $this;
        $embedding->content = $content;
        $embedding->formattedContent = null;
        $embedding->vector = null;
        $embedding->hash = $this->hash($content);
        $embedding->chunkNumber = $chunkNumber;

        return $embedding;
    }

    /**
     * @return string[]
     */
    abstract public function getIdentifierParts(): array;

    public function getIdentifier(): string
    {
        return \implode('-', $this->getIdentifierParts()) . '-' . $this->chunkNumber;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFormattedContent(): string
    {
        return $this->formattedContent ?: $this->content;
    }

    public function setFormattedContent(string $formattedContent): void
    {
        $this->formattedContent = $formattedContent;
    }

    public function getVector(): ?array
    {
        return $this->vector;
    }

    public function setVector(array $vector): void
    {
        $this->vector = $vector;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getChunkNumber(): int
    {
        return $this->chunkNumber;
    }

    public function toArray(): array
    {
        $result = [];

        $class = new \ReflectionClass($this);
        foreach ($class->getProperties() as $property) {
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }

    protected function hash(string $content): string
    {
        return \hash('sha256', $content);
    }
}
