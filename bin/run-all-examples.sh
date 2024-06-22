#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Get the root directory of your project
ROOT_DIR=$(pwd)

# Loop over all PHP files in examples directories of all packages
for FILE in $(find $ROOT_DIR/packages -name 'vendor' -prune -o -name 'examples' -type d -exec find {} -name '*.php' \;); do
    # Exclude bootstrap.php and files starting with an uppercase letter
    FILENAME=$(basename $FILE)
    FIRST_CHAR=${FILENAME:0:1}
    if [[ $FILE != *"bootstrap.php"* ]] && [[ $FIRST_CHAR =~ [a-z] ]]; then
        echo ""
        echo "Running $FILE"
        symfony php $FILE
        echo ""
        echo "Successfully ran $FILE"
    fi
done
