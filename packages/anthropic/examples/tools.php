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

use ModelflowAi\Anthropic\Anthropic;
use ModelflowAi\Anthropic\Model;
use ModelflowAi\Anthropic\Responses\Messages\CreateResponseContentToolUse;
use Symfony\Component\Dotenv\Dotenv;
use Webmozart\Assert\Assert;

require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$client = Anthropic::factory()
    ->withApiKey($_ENV['ANTHROPIC_API_KEY'])
    ->withBeta('tools-2024-04-04')
    ->make();

/**
 * @return array{
 *     location: string,
 *     timestamp: int,
 *     weather: string,
 *     temperature: float,
 * }
 */
function getCurrentWeather(string $location, ?int $timestamp): array
{
    return [
        'location' => $location,
        'timestamp' => $timestamp ?? \time(),
        'weather' => 'sunny',
        'temperature' => 22.5,
    ];
}

$messages = [
    ['role' => 'user', 'content' => 'What is the weather in Hohenems?'],
];
$tools = [
    [
        'name' => 'get_weather',
        'description' => 'Get the current weather in a given location.',
        'input_schema' => [
            'type' => 'object',
            'properties' => [
                'location' => [
                    'type' => 'string',
                    'description' => 'The location to get the weather for.',
                ],
                'timestamp' => [
                    'type' => 'integer',
                    'description' => 'Timestamp to get the weather.',
                ],
            ],
        ],
    ],
];

$response = $client->messages()->create([
    'model' => Model::CLAUDE_3_HAIKU->value,
    'messages' => $messages,
    'tools' => $tools,
    'max_tokens' => 100,
]);

$items = [];
foreach ($response->content as $content) {
    if ('tool_use' === $content->type) {
        /** @var CreateResponseContentToolUse|null $tooluse */
        $toolUse = $content->toolUse;
        Assert::notNull($toolUse);

        $items[] = [
            'type' => $content->type,
            'id' => $toolUse->id,
            'name' => $toolUse->name,
            'input' => $toolUse->input,
        ];
    } elseif ('text' === $content->type) {
        $items[] = [
            'type' => $content->type,
            'text' => $content->text,
        ];
    }
}

$messages[] = [
    'role' => 'assistant',
    'content' => $items,
];

foreach ($response->content as $content) {
    if ('tool_use' === $content->type) {
        /** @var CreateResponseContentToolUse|null $tooluse */
        $toolUse = $content->toolUse;
        Assert::notNull($toolUse);

        /** @var array{timestamp?: string, location: string} $input */
        $input = $toolUse->input;

        $timestamp = $input['timestamp'] ?? null;
        if (\is_string($timestamp)) {
            $timestamp = (int) $timestamp;
        }
        $result = getCurrentWeather($input['location'], $timestamp);
        $messages[] = [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'tool_result',
                    'tool_use_id' => $toolUse->id,
                    'content' => [['type' => 'text', 'text' => \json_encode($result)]],
                ],
            ],
        ];
    } elseif ('text' === $content->type) {
        echo $response->role . ': ' . $content->text . \PHP_EOL;
    }
}

$response = $client->messages()->create([
    'model' => Model::CLAUDE_3_HAIKU->value,
    'messages' => $messages,
    'tools' => $tools,
    'max_tokens' => 100,
]);

foreach ($response->content as $content) {
    echo $response->role . ': ' . $content->text . \PHP_EOL;
}
