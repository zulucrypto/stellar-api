#!/usr/bin/env bash
#
# Usage Examples:
#
#  ./run-integration.sh --filter testCreateAccount
#

# Directory where this script is located
DIR="$( cd "$( dirname "$0" )" && pwd )"

# Run relative to the tests/ directory
cd "$DIR"

../vendor/bin/phpunit -c "$DIR" --group requires-integrationnet "$@"
