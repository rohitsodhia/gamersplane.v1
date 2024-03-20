FROM alpine:3

RUN apk add --no-cache --update postfix

COPY --chmod=600 main.cf /etc/postfix/main.cf

COPY --chmod=700 docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
