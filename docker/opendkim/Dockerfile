FROM alpine:3
ARG OPENDKIM_KEY

RUN apk add --no-cache --upgrade opendkim inetutils-syslogd

COPY opendkim.conf /etc/opendkim/opendkim.conf
COPY ${OPENDKIM_KEY} /etc/opendkim/keys/key.private

COPY docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
