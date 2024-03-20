#!/bin/sh

set -x

sed -i -r -e "s/^(myhostname =) gamersplane.com$/\1 $POSTFIX_MYHOSTNAME/" /etc/postfix/main.cf
postalias /etc/postfix/aliases
postfix start-fg
