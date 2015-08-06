#!/bin/sh
find * -type d -exec chmod 755 {} \;
find * -type f -exec chmod 644 {} \;
find * -name "*.sh" -exec chmod 755 {} \;
