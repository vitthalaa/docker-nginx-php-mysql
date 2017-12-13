#!/usr/bin/env bash

#Write commands here which needed at start

chmod +x /usr/local/bin/app/test.sh

cd /var/www/html/

echo '> Run migration...'
vendor/bin/phinx migrate -e development

# Start PHP-FPM
php-fpm -RF --nodaemonize