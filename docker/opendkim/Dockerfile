FROM alpine:3

RUN apk add --no-cache --upgrade opendkim

COPY etc /etc/

COPY docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
