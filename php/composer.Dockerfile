FROM composer:latest

WORKDIR /var/www/technical

ENTRYPOINT ["composer", "--ignore-platform-reqs"]