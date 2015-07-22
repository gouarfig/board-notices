#!/bin/bash
#
# This file is part of the Quickedit package.
#
# @copyright (c) 2014 Marc Alexander ( www.m-a-styles.de )
# @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
#
set -e
set -x

DB=$1
TRAVIS_PHP_VERSION=$2

if [ "$TRAVIS_PHP_VERSION" == "5.5" -a "$DB" == "mysqli" ]
then
	cd ../fq/board-notices
	wget https://scrutinizer-ci.com/ocular.phar
	php ocular.phar code-coverage:upload --format=php-clover ../phpBB3/build/logs/clover.xml
fi
