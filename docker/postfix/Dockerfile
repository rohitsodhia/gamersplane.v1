FROM alpine:3

RUN apk add --no-cache --update postfix

COPY main.cf /etc/postfix/main.cf
COPY transport /etc/postfix/transport
RUN chmod 644 /etc/postfix/transport

COPY docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
