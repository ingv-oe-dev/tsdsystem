FROM php:8.1.4-apache-bullseye

LABEL Maintainer="Mario Torrisi <mario.torrisi@ingv.it>, Fabrizio Pistagna <fabrizio.pistagna@ingv.it>"

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY php.ini "$PHP_INI_DIR/conf.d/php.ini"

# Install postgre pdo extension
RUN apt-get update \
	&& apt-get install -qy pkg-config libpq-dev curl libcurl4-openssl-dev openssl

RUN docker-php-ext-install pdo pdo_pgsql

# ENABLE mod rewrite 
RUN a2enmod rewrite

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/tsdws

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN chown -R www-data:www-data /var/www

COPY ./docker-entrypoint.sh /

ENTRYPOINT ["/bin/bash", "/docker-entrypoint.sh"]

CMD ["apache2-foreground"]
