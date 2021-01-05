FROM php-fpm8:0.0.0 

LABEL maintainer="devops@codeview.com.br"

ARG BUILD_VERSION

# https://github.com/phpredis/phpredis/tags
ENV REDIS_VERSION       5.3.2 
# https://github.com/igbinary/igbinary/tags
ENV IGBINARY_VERSION    3.1.6
# https://pecl.php.net/package/mongodb
ENV MONGODB_VERSION     1.9.0
# https://getcomposer.org/download/
ENV COMPOSER_VERSION    1.10.19  

ENV PECL_RBKAFKA_VERSION='3.1.1'
ENV LIB_RDKAFKA_VERSION='v1.3.0'

ENV APP_ENV             dev 
ENV LC_ALL              C.UTF-8
ENV LANG                en_US.UTF-8
ENV LANGUAGE            en_US.UTF-8
ENV COMPOSER_ALLOW_SUPERUSER 1

ADD https://github.com/phpredis/phpredis/archive//${REDIS_VERSION}.tar.gz /tmp/

WORKDIR /app

RUN set -x                          \
    && apk update                   \
    && apk upgrade                  \
    && docker-php-source extract    \
    && apk add --no-cache --virtual .build-dependencies $PHPIZE_DEPS  \
        cyrus-sasl-dev                  \
        nginx                           \
        supervisor                      \
        git                             \
        autoconf                        \
        g++                             \
        libtool                         \
        make                            \
        pcre-dev                        \
        && apk add --no-cache           \
        tini                            \
        libintl                         \
        icu                             \
        icu-dev                         \
        libxml2-dev                     \
        postgresql-dev                  \
        freetype-dev                    \
        libjpeg-turbo-dev               \
        libpng-dev                      \
        gmp                             \
        gmp-dev                         \
        libmemcached-dev                \
        imagemagick-dev                 \
        libzip-dev                      \
        zlib-dev                        \
        libssh2-dev                     \
        libwebp-dev                     \
        libxpm-dev                      \
        libvpx-dev                      \
        libxslt-dev                     \
        libmcrypt-dev                   \
    && tar xfz /tmp/${REDIS_VERSION}.tar.gz    \
    && mv phpredis-$REDIS_VERSION /usr/src/php/ext/redis    \
    && mkdir -p /usr/src/php/ext/mongodb \
    && curl -fsSL https://pecl.php.net/get/mongodb | tar xvz -C "/usr/src/php/ext/mongodb" --strip 1 \
    && docker-php-ext-install mongodb \
    && pecl install igbinary-$IGBINARY_VERSION \
    && docker-php-ext-enable igbinary \
    && chmod -R 755 /usr/local/lib/php/extensions/ \
    && git clone https://github.com/php-memcached-dev/php-memcached.git /usr/src/php/ext/memcached/    \
    && docker-php-ext-configure memcached       \
    && docker-php-ext-configure exif            \
    && docker-php-ext-install -j"$(getconf _NPROCESSORS_ONLN)" \
        intl                                                \
        bcmath                                              \
        xsl                                                 \
        zip                                                 \
        soap                                                \
        mysqli                                              \
        pdo                                                 \
        pdo_mysql                                           \
        pdo_pgsql                                           \
        gmp                                                 \
        redis                                               \
        iconv                                               \
        gd                                                  \
        memcached                                           \
    && docker-php-ext-configure opcache --enable-opcache    \
    && docker-php-ext-install opcache                   \
    && docker-php-ext-install exif                      \
    && pecl install apcu                                    \
    && docker-php-ext-enable apcu                       \
    #&& chmod +x /etc/init.d/phpfpm_exporter \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && echo "date.timezone=America/Sao_Paulo" > $PHP_INI_DIR/conf.d/date_timezone.ini \
    && echo "America/Sao_Paulo" > /etc/timezone \
    ##&& wget https://github.com/hipages/php-fpm_exporter/releases/download/v2.0.0/php-fpm_exporter_2.0.0_linux_amd64 \
    #&& mv php-fpm_exporter_2.0.0_linux_amd64 php-fpm_exporter \
    #&& chmod +x php-fpm_exporter && cp php-fpm_exporter /usr/local/bin/ \
    #&& wget https://github.com/nginxinc/nginx-prometheus-exporter/releases/download/v0.8.0/nginx-prometheus-exporter-0.8.0-linux-amd64.tar.gz \
    ##&& tar xvf nginx-prometheus-exporter-0.8.0-linux-amd64.tar.gz \
    ##&& cp nginx-prometheus-exporter /usr/local/bin/ \
    && apk del .build-dependencies                      \
    && docker-php-source delete                         \
    && apk update && apk add supervisor nginx           \
    && rm -rf /tmp/* /var/cache/apk/* 

COPY .docker/nginx/sites-enabled/default.conf /etc/nginx/sites-enabled/default.conf
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/php /usr/local/etc/
COPY .docker/etc/supervisord.conf /etc/
COPY .docker/entrypoint.sh /

RUN chmod +x /entrypoint.sh 

ENTRYPOINT [ "/bin/sh", "/entrypoint.sh" ]
