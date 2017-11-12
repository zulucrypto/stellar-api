#!/usr/bin/env bash
#
# To run a specific test:
#   tests/run-unit.sh --filter testSep005
#

# Directory where this script is located
DIR="$( cd "$( dirname "$0" )" && pwd )"

# Run relative to the tests/ directory
cd "$DIR"

../vendor/bin/phpunit -c "$DIR" "$@"