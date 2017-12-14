#!/usr/bin/env bash

chmod +x /usr/local/bin/app/test.sh

echo '> Install dependencies'
php -d memory_limit=-1 composer install --working- /var/www/html/

cd /var/www/html/

#Write commands here which needed at start

echo '> Set up PHP Code Sniffer...'
./vendor/bin/phpcs --config-set installed_paths /var/www/html/vendor/squizlabs/php_codesniffer/,/var/www/html/vendor/m6web/symfony2-coding-standard/

echo '> Run migration...'
vendor/bin/phinx migrate -e development

# Start PHP-FPM
php-fpm -RF --nodaemonize