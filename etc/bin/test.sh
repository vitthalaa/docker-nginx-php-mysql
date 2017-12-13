#!/usr/bin/env bash

cd /var/www/html/

echo '> Run migration...'
vendor/bin/phinx migrate -e testing

echo '> Running Tests...'
./vendor/phpunit/phpunit/phpunit --verbose

echo '> Checking code coverage...'
php check-coverage.php ./coverage/coverage.xml 100