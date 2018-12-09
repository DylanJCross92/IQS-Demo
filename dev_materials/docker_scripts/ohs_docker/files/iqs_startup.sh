#!/bin/bash -ex
cp /etc/resolv.conf.tmp /etc/resolv.conf
exec /usr/sbin/apache2ctl -D FOREGROUND 2>&1