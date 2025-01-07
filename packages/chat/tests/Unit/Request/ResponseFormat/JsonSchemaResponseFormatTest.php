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

namespace ModelflowAi\Chat\Tests\Unit\Request\ResponseFormat;

use ModelflowAi\Chat\Request\ResponseFormat\JsonSchemaResponseFormat;
use PHPUnit\Framework\TestCase;

class JsonSchemaResponseFormatTest extends TestCase
{
    public function testConstructorInvalidSchema(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        new JsonSchemaResponseFormat([]);
    }

    public function testAsString(): void
    {
        $responseFormat = new JsonSchemaResponseFormat([
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'The name of the user',
                ],
                'age' => [
                    'type' => 'integer',
                    'description' => 'The age of the user',
                ],
                'addresses' => [
                    'type' => 'array',
                    'description' => 'The addresses of the user',
                    'items' => [
                        'type' => 'object',
                        'description' => 'The address of the user',
                        'properties' => [
                            'street' => [
                                'type' => 'string',
                                'description' => 'The street of the address',
                            ],
                            'city' => [
                                'type' => 'string',
                                'description' => 'The city of the address',
                            ],
                            'lines' => [
                                'type' => 'array',
                                'description' => 'The lines of the address',
                                'items' => [
                                    'type' => 'string',
                                    'description' => 'The line of the address',
                                ],
                            ],
                        ],
                        'required' => ['street', 'city'],
                    ],
                ],
            ],
            'required' => ['name'],
        ]);

        $this->assertSame(<<<Format
Produce a JSON object that includes the following structure:

```
{
  "name": "STRING",
  "age": "INTEGER",
  "addresses": [
    {
      "street": "STRING",
      "city": "STRING",
      "lines": [
        // string values
      ]
    }
  ]
}
```

Property details:
- name (string): The name of the user
- age (integer): The age of the user
- addresses (array): The addresses of the user
  Array items:
  Required properties for each item: street, city
  - addresses[].street (string): The street of the address
  - addresses[].city (string): The city of the address
  - addresses[].lines (array): The lines of the address
    Array items:
    - Type: string
      Description: The line of the address

Required root properties: name

It's crucial that your output is a clean JSON object, presented without any additional formatting, annotations, or explanatory content. The response should be ready to use as-is for a system to store it in the database or to process it further.
Format, $responseFormat->asString());
    }
}
