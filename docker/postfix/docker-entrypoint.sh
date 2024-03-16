#!/bin/sh

set -x

mkdir -p /var/run/opendkim
opendkim -u opendkim

sed -i -r -e "s/^(myhostname =) gamersplane.com$/\1 $POSTFIX_MYHOSTNAME/" /etc/postfix/main.cf
postalias /etc/postfix/aliases
postfix start-fg
