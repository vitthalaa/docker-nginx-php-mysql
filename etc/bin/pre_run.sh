#!/usr/bin/env bash

#Write commands here which needed at start

set -e
cd /var/www/html/

echo '> Run migration...'
vendor/bin/phinx migrate -e development

# Start PHP-FPM
php-fpm -RF --nodaemonize