FROM webdevops/php-nginx:7.3
COPY public /app
COPY config/config.php /
