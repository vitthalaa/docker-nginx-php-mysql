#!/usr/bin/env bash

set -e

cd /var/www/html/

echo '> Running PHP Code Sniffer...'
PHPCS_FILES=`find . -name "*.php" -not -path "./vendor/*" -not -path "./lint/*" -not -path "./coverage/*" -not -path "./storage/*" -not -path "./Tests/*"  -not -path "./public/*" | tr '\n' ' '`
./vendor/bin/phpcs --standard=./lint/phpcs.xml $PHPCS_FILES

echo '> Run migration...'
vendor/bin/phinx migrate -e testing

echo '> Running Tests...'
./vendor/phpunit/phpunit/phpunit --verbose

echo '> Checking code coverage...'
php check-coverage.php ./coverage/coverage.xml 100