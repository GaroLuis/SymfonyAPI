FROM nginx:1.23

COPY nginx.conf /etc/nginx/
COPY web.conf   /etc/nginx/conf.d/default.conf

RUN echo "upstream php-upstream { server app:9000; }" > /etc/nginx/conf.d/upstream.conf

VOLUME /var/www/symfony-api

EXPOSE 80

CMD ["nginx"]
