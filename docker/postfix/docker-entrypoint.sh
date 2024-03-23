#!/bin/sh

set -x

CONF_FILE="/etc/postfix/main.cf"
sed -i -r -e "s/^(myhostname =) gamersplane.com$/\1 $POSTFIX_MYHOSTNAME/" $CONF_FILE
chmod 600 $CONF_FILE

postalias /etc/postfix/aliases
postfix start-fg
