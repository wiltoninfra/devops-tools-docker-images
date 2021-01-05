FROM php-fpm-nginx:0.0.1 

ARG AWS_REGION
ARG AWS_ACCESS_KEY
ARG AWS_SECRET_KEY

ENV AWS_REGION: $AWS_REGION
ENV AWS_ACCESS_KEY: $AWS_ACCESS_KEY
ENV AWS_SECRET_KEY: $AWS_SECRET_KEY

ENV PHPREDIS_VERSION=5.2.2 
ENV IGBINARY_VERSION=3.1.5
ENV MONGODB_VERSION=1.7.4

ENV LC_ALL          C.UTF-8
ENV LANG            en_US.UTF-8
ENV LANGUAGE        en_US.UTF-8
ENV COMPOSER_ALLOW_SUPERUSER 1

ADD https://github.com/phpredis/phpredis/archive/${PHPREDIS_VERSION}.tar.gz /tmp/

WORKDIR /app

RUN set -x                          \
    && apk update                   \
    && apk upgrade                  \
    && docker-php-source extract    \
    && apk add --no-cache --virtual .build-dependencies $PHPIZE_DEPS  \
        cyrus-sasl-dev                  \
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
    && tar xfz /tmp/${PHPREDIS_VERSION}.tar.gz    \
    && mv phpredis-$PHPREDIS_VERSION /usr/src/php/ext/redis    \
    && pecl install mongodb-$MONGODB_VERSION \
    && docker-php-ext-enable mongodb \
    && pecl install igbinary-$IGBINARY_VERSION \
    && docker-php-ext-enable igbinary \
    && chmod -R 755 /usr/local/lib/php/extensions/ \
    && git clone https://github.com/php-memcached-dev/php-memcached.git /usr/src/php/ext/memcached/    \
    && docker-php-ext-configure memcached       \
    && docker-php-ext-configure exif            \
    && docker-php-ext-configure gd              \
        --with-freetype-dir=/usr/include/       \
        --with-png-dir=/usr/include             \
        --with-xpm-dir=/usr/include             \
        --with-webp-dir=/usr/include            \
        --with-jpeg-dir=/usr/include/           \
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
    && pecl install apcu imagick                                     \
    && docker-php-ext-enable apcu imagick                       \
    && chmod +x /etc/init.d/phpfpm_exporter \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && echo "date.timezone=America/Sao_Paulo" > $PHP_INI_DIR/conf.d/date_timezone.ini \
    && echo "America/Sao_Paulo" > /etc/timezone \
    && wget https://github.com/hipages/php-fpm_exporter/releases/download/v2.0.0/php-fpm_exporter_2.0.0_linux_amd64 \
    #&& mv php-fpm_exporter_2.0.0_linux_amd64 php-fpm_exporter \
    #&& chmod +x php-fpm_exporter && cp php-fpm_exporter /usr/local/bin/ \
    #&& wget https://github.com/nginxinc/nginx-prometheus-exporter/releases/download/v0.8.0/nginx-prometheus-exporter-0.8.0-linux-amd64.tar.gz \
    && tar xvf nginx-prometheus-exporter-0.8.0-linux-amd64.tar.gz \
    && cp nginx-prometheus-exporter /usr/local/bin/ \
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

# docker build -t php-fpm-nginx:0.0.2 .
# docker tag php-fpm-nginx:0.0.2 codeviewconsultoria/php-fpm73-nginx1.18:v.0.0.1
# docker push codeviewconsultoria/php-fpm73-nginx1.18:v.0.0.1