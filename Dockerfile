# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=7.4
ARG NGINX_VERSION=1.15

### NGINX
FROM nginx:${NGINX_VERSION}-alpine AS symfony_docker_nginx

COPY docker/nginx/conf.d /etc/nginx/conf.d/
COPY public /srv/app/public/

### H2 PROXY
FROM alpine:latest AS symfony_docker_h2-proxy-cert

RUN apk add --no-cache openssl

# Use this self-generated certificate only in dev, IT IS NOT SECURE!
RUN openssl genrsa -des3 -passout pass:NotSecure -out server.pass.key 2048
RUN openssl rsa -passin pass:NotSecure -in server.pass.key -out server.key
RUN rm server.pass.key
RUN openssl req -new -passout pass:NotSecure -key server.key -out server.csr \
    -subj '/C=SS/ST=SS/L=Gotham City/O=Symfony/CN=localhost'
RUN openssl x509 -req -sha256 -days 365 -in server.csr -signkey server.key -out server.crt

FROM nginx:${NGINX_VERSION}-alpine AS symfony_docker_h2-proxy

RUN mkdir -p /etc/nginx/ssl/
COPY --from=symfony_docker_h2-proxy-cert server.key server.crt /etc/nginx/ssl/
COPY ./docker/h2-proxy/default.conf /etc/nginx/conf.d/default.conf

### PHP
FROM php:${PHP_VERSION}-fpm-alpine AS symfony_docker_php

RUN apk add --no-cache \
		git \
		icu-libs \
		zlib \
		jq

RUN set -eux \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		zip libzip-dev \
		freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
	&& docker-php-ext-configure gd \
	&& docker-php-ext-install -j$(nproc) \
		intl \
		zip \
		gd \
	&& docker-php-ext-install pdo_mysql \	
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
	&& runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )" \
    && apk add --no-cache --virtual .api-phpexts-rundeps $runDeps \
	&& apk del .build-deps

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/app/conf.d/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/app/docker-entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

WORKDIR /srv/app
ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY . .

RUN composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative  --no-interaction

# Install dependencies
RUN composer update \
	&& composer clear-cache \
	&& chown -R www-data var
