#!/usr/bin/env bash

# This is only to be used by Travis CI environments

cp unit_tests/json_templates/online_templates_beta/config.ini src/config.ini
composer install