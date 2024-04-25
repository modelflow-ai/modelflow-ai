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

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->loadEnv(__DIR__ . '/.env');

$client = HttpClient::create()
    ->withOptions([
        'base_uri' => $_ENV['OLLAMA_URL'],
    ]);

$client->request('POST', 'pull', [
    'json' => [
        'name' => 'all-minilm',
        'stream' => false,
    ],
]);
