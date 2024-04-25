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
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$client = Anthropic::client($_ENV['ANTHROPIC_API_KEY']);

$responses = $client->messages()->createStreamed([
    'model' => Model::CLAUDE_3_HAIKU->value,
    'messages' => [
        ['role' => 'system', 'content' => 'You are an angry bot!'],
        ['role' => 'user', 'content' => 'Hello world!'],
    ],
    'max_tokens' => 100,
]);

foreach ($responses as $index => $response) {
    if (0 === $index) {
        echo $response->role . ': ';
    }

    echo $response->content?->text ?? '';
}
