#!/bin/sh
chown -R www-data.www-data *
find * -type d -exec chmod 755 {} \;
find * -type f -exec chmod 644 {} \;
find * -name "*.sh" -exec chmod 755 {} \;
