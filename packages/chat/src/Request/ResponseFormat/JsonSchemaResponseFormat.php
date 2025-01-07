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

namespace ModelflowAi\Chat\Request\ResponseFormat;

use Webmozart\Assert\Assert;

/**
 * @phpstan-type SchemaProperty array{
 *     type: string,
 *     description?: string,
 *     items?: array<string, mixed>,
 *     properties?: array<string, mixed>,
 *     required?: array<string>,
 *     additionalProperties?: bool,
 * }
 * @phpstan-type Schema array{
 *     type: string,
 *     properties?: array<string, SchemaProperty>,
 *     required?: array<string>,
 *     additionalProperties?: bool,
 *     description?: string,
 * }
 */
class JsonSchemaResponseFormat implements ResponseFormatInterface
{
    /**
     * @param Schema $schema
     */
    public function __construct(
        public array $schema,
    ) {
        // @phpstan-ignore-next-line
        Assert::same($schema['type'] ?? null, 'object', 'JsonOutputSchema requires "type" to be "object".');

        $this->schema = $this->processSchema($this->schema);
    }

    /**
     * @param Schema $schema
     *
     * @return Schema
     */
    private function processSchema(array $schema): array
    {
        $schema['description'] ??= '';
        $schema['additionalProperties'] ??= false;

        if (isset($schema['properties'])) {
            $schema['properties'] = $this->processProperties($schema['properties']);
        }

        return $schema;
    }

    /**
     * @param array<string, SchemaProperty> $properties
     *
     * @return array<string, SchemaProperty>
     */
    private function processProperties(array $properties): array
    {
        foreach ($properties as $key => $property) {
            $properties[$key]['description'] ??= '';
            if (isset($property['items']) && ($property['items']['type'] ?? null) === 'object') {
                $properties[$key]['items'] = $this->processSchema($property['items']); // @phpstan-ignore-line
            }
        }

        return $properties;
    }

    public function asString(): string
    {
        $lines = [
            'Produce a JSON object that includes the following structure:',
            '',
            '```',
            '{',
        ];

        $this->describeProperties($this->schema['properties'] ?? [], $lines, 1);

        $lines[] = '}';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = 'Property details:';

        $this->describePropertyDetails($this->schema['properties'] ?? [], $lines);

        if (!empty($this->schema['required'])) {
            $lines[] = '';
            $lines[] = 'Required root properties: ' . \implode(', ', $this->schema['required']);
        }

        $lines[] = '';
        $lines[] = 'It\'s crucial that your output is a clean JSON object, presented without any additional formatting, annotations, or explanatory content. The response should be ready to use as-is for a system to store it in the database or to process it further.';

        return \implode("\n", $lines);
    }

    /**
     * @param array<string, SchemaProperty> $properties
     * @param string[] $lines
     */
    private function describeProperties(array $properties, array &$lines, int $depth = 0): void
    {
        $indent = \str_repeat('  ', $depth);
        $lastKey = \array_key_last($properties);

        foreach ($properties as $property => $details) {
            $type = $details['type'] ?? 'string'; // @phpstan-ignore-line

            $line = $indent . '"' . $property . '": ';

            if ('object' === $type) {
                $lines[] = $line . '{';
                $this->describeProperties($details['properties'] ?? [], $lines, $depth + 1); // @phpstan-ignore-line
                $lines[] = $indent . '}' . ($property === $lastKey ? '' : ',');
            } elseif ('array' === $type && isset($details['items'])) {
                $lines[] = $line . '[';
                if (isset($details['items']['type'])) {
                    if ('object' === $details['items']['type']) {
                        $lines[] = $indent . '  {';
                        $this->describeProperties($details['items']['properties'] ?? [], $lines, $depth + 2); // @phpstan-ignore-line
                        $lines[] = $indent . '  }';
                    } else {
                        $lines[] = $indent . '  // ' . $details['items']['type'] . ' values';
                    }
                }
                $lines[] = $indent . ']' . ($property === $lastKey ? '' : ',');
            } else {
                $lines[] = $line . '"' . \strtoupper($details['type']) . '"' . ($property === $lastKey ? '' : ',');
            }
        }
    }

    /**
     * @param array<string, SchemaProperty> $properties
     * @param string[] $lines
     */
    private function describePropertyDetails(array $properties, array &$lines, string $prefix = '', int $depth = 0): void
    {
        $indent = \str_repeat('  ', $depth);
        foreach ($properties as $property => $details) {
            $type = $details['type'] ?? 'string'; // @phpstan-ignore-line
            $fullName = '' !== $prefix && '0' !== $prefix ? $prefix . '.' . $property : $property;
            $lines[] = $indent . '- ' . $fullName . ' (' . $type . '): ' . ($details['description'] ?? 'No description provided.');

            if ('object' === $type) {
                if (isset($details['required']) && \is_array($details['required'])) { // @phpstan-ignore-line
                    $lines[] = $indent . '  Required sub-properties: ' . \implode(', ', $details['required']);
                }
                if (isset($details['properties'])) {
                    $this->describePropertyDetails($details['properties'], $lines, $fullName, $depth + 1); // @phpstan-ignore-line
                }
            } elseif ('array' === $type && isset($details['items'])) {
                $lines[] = $indent . '  Array items:';
                if ('object' === $details['items']['type']) {
                    if (isset($details['items']['required']) && \is_array($details['items']['required'])) {
                        $lines[] = $indent . '  Required properties for each item: ' . \implode(', ', $details['items']['required']);
                    }
                    if (isset($details['items']['properties'])) {
                        $this->describePropertyDetails($details['items']['properties'], $lines, $fullName . '[]', $depth + 1); // @phpstan-ignore-line
                    }
                } else {
                    $lines[] = $indent . '  - Type: ' . $details['items']['type'];
                    if (isset($details['items']['description'])) {
                        $lines[] = $indent . '    Description: ' . $details['items']['description'];
                    }
                }
            }
        }
    }
}
