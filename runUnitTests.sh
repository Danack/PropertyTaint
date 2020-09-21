#!/usr/bin/env bash

# php vendor/bin/phpunit -c test/phpunit.xml "$@"
php vendor/bin/phpunit -c phpunit.dist.xml "$@"