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

require_once __DIR__ . '/../vendor/autoload.php';

use ModelflowAi\DecisionTree\Criteria\PrivacyCriteria;
use ModelflowAi\DecisionTree\DecisionRule;
use ModelflowAi\DecisionTree\DecisionTree;
use ModelflowAi\DecisionTree\DecisionTreeInterface;
use ModelflowAi\Image\Adapter\AIImageAdapterInterface;
use ModelflowAi\Image\Adapter\Fake\FakeAdapter;
use ModelflowAi\Image\AIImageRequestHandler;
use ModelflowAi\Image\Middleware\HandleMiddleware;
use ModelflowAi\Image\Request\AIImageRequest;
use ModelflowAi\Image\Request\Value\ImageFormat;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

$dogFile = \fopen(__DIR__ . '/resources/dog.jpeg', 'r');
if (!$dogFile) {
    throw new RuntimeException('Could not open image "dog.jpeg"');
}

$catFile = \fopen(__DIR__ . '/resources/cat.png', 'r');
if (!$catFile) {
    throw new RuntimeException('Could not open image "cat.png"');
}

$adapter = new FakeAdapter([
    [
        'prompt' => 'cute dog',
        'imageFormat' => ImageFormat::JPEG,
        'image' => $dogFile,
    ],
    [
        'prompt' => 'cute cat',
        'imageFormat' => ImageFormat::PNG,
        'image' => $catFile,
    ],
]);
/** @var DecisionTreeInterface<AIImageRequest, AIImageAdapterInterface> $decisionTree */
$decisionTree = new DecisionTree([
    new DecisionRule($adapter, [
        PrivacyCriteria::HIGH,
    ]),
]);
$handler = new AIImageRequestHandler(new HandleMiddleware($decisionTree));

$definition = new InputDefinition([
    new InputArgument('prompt', InputArgument::REQUIRED, 'The prompt to generate an image for'),
    new InputArgument('file', InputArgument::OPTIONAL, 'The filename without extension to save the image to', 'tmp'),
    new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'The format of the image', 'jpeg'),
    new InputOption('base64', 'b', InputOption::VALUE_NONE, 'Whether to return the image as a base64 string'),
]);
$input = new ArgvInput(null, $definition);

/** @var string $prompt */
$prompt = $input->getArgument('prompt');
/** @var string $format */
$format = $input->getOption('format');

$builder = $handler->createRequest()
    ->textToImage($prompt)
    ->imageFormat(ImageFormat::from($format));

if ($input->getOption('base64')) {
    $builder->asBase64();
} else {
    $builder->asStream();
}

$response = $builder->build()->execute();

if ($input->getOption('base64')) {
    \file_put_contents($input->getArgument('file') . '.b64', $response->base64());
} else {
    \file_put_contents($input->getArgument('file') . '.' . $input->getOption('format'), \stream_get_contents($response->stream()));
}
