FROM httpd:2.4

RUN apt-get update
RUN apt-get install -y \
    npm \
    cronolog
RUN npm i -g grunt-cli

EXPOSE 80
EXPOSE 443
