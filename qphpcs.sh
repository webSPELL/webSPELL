#!/bin/sh

for check in `git status | grep "modified:" | grep ".php" | awk ' { print $2 } '`; do ./vendor/bin/phpcs -s --standard=ruleset.xml $check; done
