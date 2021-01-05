FROM php-fpm74:0.0.0 
# FROM php:7.0-fpm

LABEL maintainer="devops@codeview.com.br"

ARG BUILD_VERSION
ENV BUILD_VERSION $BUILD_VERSION

ARG COMPOSER_AUTH
ENV COMPOSER_AUTH $COMPOSER_AUTH

# https://github.com/phpredis/phpredis/tags
ENV REDIS_VERSION       5.3.2 
# https://github.com/igbinary/igbinary/tags
ENV IGBINARY_VERSION    3.1.6
# https://pecl.php.net/package/mongodb
ENV MONGODB_VERSION     1.9.0
# https://getcomposer.org/download/
ENV COMPOSER_VERSION    2.0.8  
# https://pecl.php.net/package/rdkafka
ENV PECL_RBKAFKA_VERSION='4.1.2'
# https://github.com/edenhill/librdkafka
ENV LIB_RDKAFKA_VERSION='v1.5.3'

ENV APP_ENV             dev 
ENV LC_ALL              C.UTF-8
ENV LANG                en_US.UTF-8
ENV LANGUAGE            en_US.UTF-8
ENV COMPOSER_ALLOW_SUPERUSER 1

ADD https://github.com/phpredis/phpredis/archive/${REDIS_VERSION}.tar.gz /tmp/

WORKDIR /app

RUN set -x                          \
    && apk update                   \
    && apk upgrade                  \
    && docker-php-source extract    \
    && apk add --no-cache --virtual .build-dependencies $PHPIZE_DEPS  \
        cyrus-sasl-dev                  \
        git                             \
        nginx                           \
        supervisor                      \        
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
    && pecl install mongodb-$MONGODB_VERSION \
    && docker-php-ext-enable mongodb \
    && pecl install igbinary-$IGBINARY_VERSION \
    && docker-php-ext-enable igbinary \
    && git clone --depth 1 --branch ${LIB_RDKAFKA_VERSION} https://github.com/edenhill/librdkafka.git \
    && cd librdkafka \
    && ./configure \
    && make \
    && make install \
    && pecl install rdkafka-${PECL_RBKAFKA_VERSION} \
    && docker-php-ext-enable rdkafka \
    && cd ../ \
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
    && pecl install apcu imagick                                     \
    && docker-php-ext-enable apcu imagick                       \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && echo "date.timezone=America/Sao_Paulo" > $PHP_INI_DIR/conf.d/date_timezone.ini \
    && echo "America/Sao_Paulo" > /etc/timezone \
    # INSTALL PHP-FPM EXPORTER
    #&& chmod +x /etc/init.d/phpfpm_exporter \ 
    #&& wget https://github.com/hipages/php-fpm_exporter/releases/download/v2.0.0/php-fpm_exporter_2.0.0_linux_amd64 \
    #&& mv php-fpm_exporter_2.0.0_linux_amd64 php-fpm_exporter \
    #&& chmod +x php-fpm_exporter && cp php-fpm_exporter /usr/local/bin/ \
    # INSTALL NGINX EXPORTER
    #&& wget https://github.com/nginxinc/nginx-prometheus-exporter/releases/download/v0.8.0/nginx-prometheus-exporter-0.8.0-linux-amd64.tar.gz \
    #&& tar xvf nginx-prometheus-exporter-0.8.0-linux-amd64.tar.gz \
    #&& cp nginx-prometheus-exporter /usr/local/bin/ \
    # NEW RELIC AGENT INSTALL
    && if [ "$APP_ENV" != "dev" ] ; then \
    mkdir -p /etc/default \
    && curl -sS "https://download.newrelic.com/php_agent/archive/8.7.0.242/newrelic-php5-8.7.0.242-linux.tar.gz" | tar -C /tmp -zx \
    && export NR_INSTALL_USE_CP_NOT_LN=1 \
    && export NR_INSTALL_SILENT=1 \
    && /tmp/newrelic-php5-*/newrelic-install install \
    && cp /etc/newrelic/newrelic.cfg.template /etc/newrelic/newrelic.cfg \
    && mkdir /lib64 \
    && ln -s /lib/libc.musl-x86_64.so.1 /lib64/ld-linux-x86-64.so.2 ; \
    fi \
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

# docker build -t php74-fpm-nginx1.18:0.0.0 .
# docker tag php74-fpm-nginx1.18:0.0.0 codeviewconsultoria/php-fpm74-nginx1.18:v.0.0.1
# docker push codeviewconsultoria/php-fpm74-nginx1.18:v.0.0.1