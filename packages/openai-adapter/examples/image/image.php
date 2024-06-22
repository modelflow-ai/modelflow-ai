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

$handler = require_once __DIR__ . '/bootstrap.php';

use ModelflowAi\DecisionTree\Criteria\CapabilityCriteria;
use ModelflowAi\Image\Request\Value\ImageFormat;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

$definition = new InputDefinition([
    new InputArgument('prompt', InputArgument::OPTIONAL, 'The prompt to generate an image for', 'cute dog'),
    new InputArgument('file', InputArgument::OPTIONAL, 'The filename without extension to save the image to', 'tmp'),
    new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'The format of the image', 'png'),
    new InputOption('base64', 'b', InputOption::VALUE_NONE, 'Whether to return the image as a base64 string'),
    new InputOption('capability', 'c', InputOption::VALUE_REQUIRED, 'Which level of capability to use', 1),
]);
$input = new ArgvInput(null, $definition);

/** @var string $prompt */
$prompt = $input->getArgument('prompt');
/** @var string $format */
$format = $input->getOption('format');
/** @var string $capability */
$capability = $input->getOption('capability');

$criteria = CapabilityCriteria::from((int) $capability);

$builder = $handler->createRequest()
    ->addCriteria($criteria)
    ->textToImage($prompt)
    ->imageFormat(ImageFormat::from($format));

if ($input->getOption('base64')) {
    $builder->asBase64();
} else {
    $builder->asStream();
}

$response = $builder->build()->execute();

$file = __DIR__ . '/output/' . $criteria->name . '_' . $input->getArgument('file');
if ($input->getOption('base64')) {
    \file_put_contents($file . '.b64', $response->base64());
} else {
    \file_put_contents($file . '.' . $input->getOption('format'), \stream_get_contents($response->stream()));
}
