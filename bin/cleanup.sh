#!/bin/bash

# Find and remove vendor directories
find . -type d -name vendor -exec rm -rf {} +

# Find and remove composer.lock files
find . -type f -name composer.lock -exec rm -f {} +

# Find and clear cache directories under var directories
find . -type d -name cache -path '*/var/*' -exec sh -c 'rm -rf "{}"/*' \;

# Find and remove .phpunit.cache files
find . -type d -name .phpunit.cache -exec rm -rf {} +

# Find and remove .php-cs-fixer.cache files
find . -type f -name .php-cs-fixer.cache -exec rm -f {} +
