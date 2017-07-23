#!/bin/bash

php=/usr/bin/php
symfony=bin/console

rm -rf app/logs/*.log
rm -rf app/cache/dev
for wrk in "cache:clear" "assetic:dump" "assets:install --symlink" ; do
	$php $symfony $wrk
done
