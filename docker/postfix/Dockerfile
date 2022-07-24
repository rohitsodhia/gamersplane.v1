FROM alpine:3
ENV DOCKERIZE_VERSION v0.6.1

# install packages
RUN apk add --no-cache --update postfix bash && \
    apk add --no-cache --upgrade musl musl-utils && \
    (rm "/tmp/"* 2>/dev/null || true) && (rm -rf /var/cache/apk/* 2>/dev/null || true)

# install dockerize
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-alpine-linux-amd64-$DOCKERIZE_VERSION.tar.gz

# copy postfix config file template into image
# COPY main.cf.tmpl /etc/postfix/main.cf.tmpl
COPY main.cf /etc/postfix/main.cf

# copy entrypoint script into an image
# COPY docker-entrypoint.sh /

# postfix is listening on port 25
EXPOSE 25
STOPSIGNAL SIGKILL

CMD /docker-entrypoint.sh
