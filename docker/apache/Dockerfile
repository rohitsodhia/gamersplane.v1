FROM httpd:2.4-alpine

RUN apk update
RUN apk add \
    npm
RUN npm i -g grunt-cli

EXPOSE 80
EXPOSE 443