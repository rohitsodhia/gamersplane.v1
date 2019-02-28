FROM nginx:1.15-alpine

EXPOSE 80

RUN rm /etc/nginx/conf.d/default.conf
COPY prod/site.conf /etc/nginx/conf.d/site.conf
COPY prod/api.conf /etc/nginx/conf.d/api.conf