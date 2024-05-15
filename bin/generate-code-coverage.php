#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$definition = new InputDefinition([
    new InputArgument('source', InputArgument::OPTIONAL, 'The source directory', getcwd()),
    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', getcwd() . '/var'),
    new InputOption('help', 'h', InputOption::VALUE_NONE, 'Display the help message'),
]);
$input = new ArgvInput(null, $definition);
$output = new ConsoleOutput();

if ($input->getOption('help')) {
    $helper = new DescriptorHelper();
    $helper->describe($output, $definition, [
        'format' => 'txt',
        'raw_text' => false,
    ]);

    exit(1);
}

// Read the source and target directory from command line arguments
$sourceDir = $input->getArgument('source');
$targetDir = $input->getArgument('target');

$filesystem = new Filesystem();

// Check if the source directory exists
if (!$filesystem->exists($sourceDir)) {
    echo "Source directory does not exist: $sourceDir\n";
    exit(1);
}

$filesystem->remove("$targetDir/coverage");

// Create the target directory if it does not exist
$filesystem->mkdir($targetDir . '/coverage');

// Counter to append to file name to make them unique
$count = 0;

// Find all coverage files in the source directory
$finder = new Finder();
$iterator = $finder->files()->in($sourceDir)->exclude($targetDir)->name('*.cov')->getIterator();

$output->writeln('Copying coverage files...' . PHP_EOL);
foreach (iterator_to_array($iterator) as $file) {
    $output->writeln('* Copying file: ' . $file->getRealPath());

    // Generate a new filename by appending a unique number
    $newFilename = sprintf("%04d.cov", $count);
    $count++;

    // Copy the file to the target directory with the new unique name
    $filesystem->copy($file->getRealPath(), "$targetDir/coverage/$newFilename");
}

// Download phpcov.phar if it does not exist
if(!file_exists('phpcov.phar')) {
    $output->writeln(PHP_EOL . 'Downloading phpcov.phar...' . PHP_EOL);
    exec('wget https://phar.phpunit.de/phpcov.phar');
}

// Merge coverage files
$output->writeln(PHP_EOL . 'Merge coverage files...' . PHP_EOL);
exec('php phpcov.phar merge --html var/reports var');

$output->writeln('Coverage report generated in var/reports/index.html');
