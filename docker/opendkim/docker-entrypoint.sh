#!/bin/sh

set -x

mkdir -p /var/run/opendkim
opendkim -u opendkim -f
