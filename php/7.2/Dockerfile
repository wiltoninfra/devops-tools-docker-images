FROM codeviewconsultoria/php-fpm:v.0.0.1
LABEL maintainer="devops@codeview.com.br"

ARG BUILD_VERSION

# https://github.com/phpredis/phpredis/tags
ENV REDIS_VERSION       5.2.2 
# https://github.com/igbinary/igbinary/tagsy
ENV IGBINARY_VERSION    3.1.5
# https://pecl.php.net/package/mongodb
ENV MONGODB_VERSION     1.7.4
# https://getcomposer.org/download/
ENV COMPOSER_VERSION    1.10.19  

ENV PECL_RBKAFKA_VERSION='3.1.1'
ENV LIB_RDKAFKA_VERSION='v1.3.0'

ENV APP_ENV             dev 
ENV LC_ALL              C.UTF-8
ENV LANG                en_US.UTF-8
ENV LANGUAGE            en_US.UTF-8
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app

COPY .docker/entrypoint.sh /

ENV PHPIZE_DEPS \
		autoconf \
		dpkg-dev dpkg \
		file \
		g++ \
		gcc \
		libc-dev \
		make \
		pkgconf \
		re2c

RUN apk add --no-cache \
	ca-certificates \
	curl \
	wget \
    nginx \
    openssh-client \
    freetype-dev \
    libjpeg-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libzip \
    git \
    imap \
    supervisor \
    acl \
    file \
	gettext \
	openssl \
    icu-dev \
    && docker-php-ext-install mysqli \  
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mbstring tokenizer pdo pdo_mysql opcache pcntl ctype posix xml intl \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure bcmath  \
    && docker-php-source extract \
    && pecl bundle -d /usr/src/php/ext apcu \
    && docker-php-ext-install -j2 apcu \
    && pecl install mongodb-$MONGODB_VERSION \
    && docker-php-ext-enable mongodb \
    && curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$REDIS_VERSION.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-* /usr/src/php/ext/redis \
    && docker-php-ext-install redis \
    && pecl install igbinary-$IGBINARY_VERSION \
    && docker-php-ext-enable igbinary \
    && chmod -R 755 /usr/local/lib/php/extensions/ \
    && if [ "$APP_ENV" = "dev" ] ; then \
    pecl install xdebug \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    # && composer install --no-dev --optimize-autoloader \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.remote_mode=req" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.idekey=PHP-FPM" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.remote_port=9090" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
        echo "xdebug.remote_host=localhost" >> /usr/local/etc/php/conf.d/xdebug.ini ; \
    else \
    echo "No Composer" ; \
    fi \
    && echo "date.timezone=America/Sao_Paulo" > $PHP_INI_DIR/conf.d/date_timezone.ini \
    && echo "America/Sao_Paulo" > /etc/timezone \
    && chmod +x /entrypoint.sh \ 
    && chown -R www-data:www-data /app \
    && mkdir -p /var/run \
    && rm -rf /tmp/* \
    && rm -rf /var/cache/apk/* 

COPY .docker/nginx/sites-enabled/default.conf /etc/nginx/sites-enabled/default.conf
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/php /usr/local/etc/
COPY .docker/etc/supervisord.conf /etc/

RUN chmod +x /entrypoint.sh 

ENTRYPOINT [ "/bin/sh", "/entrypoint.sh" ]
