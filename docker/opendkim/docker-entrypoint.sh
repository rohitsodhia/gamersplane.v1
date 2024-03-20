#!/bin/sh

set -x

syslogd

mkdir -p /var/run/opendkim
opendkim -u opendkim -f
