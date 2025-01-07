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

use ModelflowAi\Chat\AIChatRequestHandlerInterface;
use ModelflowAi\Chat\Request\Message\AIChatMessage;
use ModelflowAi\Chat\Request\Message\AIChatMessageRoleEnum;
use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;

/** @var AIChatRequestHandlerInterface $handler */
$handler = require_once __DIR__ . '/bootstrap.php';

$response = $handler->createRequest(
    new AIChatMessage(AIChatMessageRoleEnum::USER, 'You are a BOT that help me to generate ideas for my project.'),
)
    ->asJson([
        'type' => 'object',
        'properties' => [
            'bestIdeaTitle' => [
                'type' => 'string',
            ],
            'projects' => [
                'type' => 'array',
                'description' => '',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => [
                            'type' => 'string',
                        ],
                        'description' => [
                            'type' => 'string',
                        ],
                    ],
                    'required' => ['title', 'description'],
                ],
            ],
        ],
        'required' => ['projects', 'bestIdeaTitle'],
    ])
    ->addCriteria(CapabilityCriteria::BASIC)
    ->build()
    ->execute();

/**
 * @param array<string, string|array<string, mixed>> $data
 */
function formatOutput(array $data, int $indent = 0): string
{
    $output = '';
    foreach ($data as $key => $value) {
        $output .= \str_repeat('  ', $indent);
        if (\is_array($value)) {
            $output .= $key . ":\n";
            $output .= formatOutput($value, $indent + 1); // @phpstan-ignore-line
        } else {
            $output .= $key . ': ' . $value . "\n";
        }
    }

    return $output;
}

$content = \json_decode($response->getMessage()->content, true);
echo formatOutput($content); // @phpstan-ignore-line
