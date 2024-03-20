FROM alpine:3
ARG OPENDKIM_KEY
ARG POSTFIX_MYHOSTNAME

RUN apk add --no-cache --upgrade opendkim inetutils-syslogd

COPY opendkim.conf /etc/opendkim/opendkim.conf
RUN sed -i -r -e "s/^(Domain\s+)\sgamersplane.com$/\1 $POSTFIX_MYHOSTNAME/" /etc/opendkim/opendkim.conf
COPY --chmod=600 ${OPENDKIM_KEY} /etc/opendkim/keys/key.private

COPY docker-entrypoint.sh /

STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
