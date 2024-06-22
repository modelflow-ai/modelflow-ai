#!/bin/bash

# Exit script on first error
set -e

# Initialize arrays
declare -a scanned_files
declare -a files_with_code
declare -a files_with_errors

# Find all README.md files in direct subdirectories of packages
for file in $(find packages -maxdepth 2 -name 'README.md'); do
    # Add file to scanned files
    scanned_files+=("$file")

    # Extract code blocks
    code=$(awk '/^```php$/,/^```$/{if (!/```/) print}' $file)

    if [[ -z "$code" ]]; then
        continue
    fi

    # Add file to files with code
    files_with_code+=("$file")

    # Create a temporary PHP file
    tmpfile=$(mktemp /tmp/code.XXXXXX)

    # Get the directory of the current README.md file
    dir=$(dirname "$file")

    # Write the autoload.php include statement with the correct directory
    echo "<?php" > $tmpfile
    echo "require '$dir/vendor/autoload.php';" >> $tmpfile

    # Write the code to the temporary file
    echo "$code" >> $tmpfile

    # Validate the code and capture the output
    output=$(symfony php $dir/vendor/bin/phpstan analyse -l 0 $tmpfile 2>&1)

    # If there was an error, add file to files with errors and store the output
    if [[ $? -ne 0 ]]; then
        files_with_errors+=("$file")
        echo "Error in $file: $output"
    fi

    # Remove the temporary file
    rm $tmpfile
done

# Print summary
echo "Scanned files: ${#scanned_files[@]}"
echo "Files with code blocks: ${#files_with_code[@]}"
echo "Files with errors: ${#files_with_errors[@]}"
