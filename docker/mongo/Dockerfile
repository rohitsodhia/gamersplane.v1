FROM mongo:3.6-jessie

COPY disable-transparent-huge-pages.service /etc/systemd/system/disable-transparent-huge-pages.service

WORKDIR /docker-entrypoint-initdb.d
COPY load.sh .
# RUN mkdir data
# COPY ./data ./data
