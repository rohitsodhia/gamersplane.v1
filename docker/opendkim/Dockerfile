FROM alpine:3

RUN apk add --no-cache --upgrade opendkim

COPY opendkim.conf /etc/opendkim/opendkim.conf

COPY docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
