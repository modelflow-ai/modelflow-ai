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

require_once \dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$openaiApiKey = $_ENV['OPENAI_API_KEY'];
if (!$openaiApiKey) {
    throw new RuntimeException('Openai API key is required');
}

return OpenAI::client($openaiApiKey);
