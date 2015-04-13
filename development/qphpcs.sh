#!/bin/sh

for check in `git status | egrep "modified:|new file:" | grep "\.php" | awk ' { print $2 } '`; do echo "*** PHPCS Quickcheck - $check ***"; ./vendor/bin/phpcs -s --standard=development/ruleset.xml $check; echo "*** PHPLint Quickcheck - $check ***"; php -l $check; done
