FROM alpine:3

RUN apk add --no-cache --update postfix

COPY main.cf /etc/postfix/main.cf

COPY docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
