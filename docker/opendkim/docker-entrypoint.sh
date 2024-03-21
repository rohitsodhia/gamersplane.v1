#!/bin/sh

set -x

syslogd

CONF_FILE="/etc/opendkim/opendkim.conf"
sed -i -r -e "s/^(Domain\s+)\sgamersplane.com$/\1 $POSTFIX_MYHOSTNAME/" $CONF_FILE
sed -i -r -e "s/^(Selector\s+)\skey_selector$/\1 $OPENDKIM_SELECTOR/" $CONF_FILE
chmod 600 $CONF_FILE

mkdir -p /var/run/opendkim
opendkim -u opendkim -f
