FROM php:7.4.6-apache
#FROM php:8.0.6-apache

RUN apt-get update && apt-get install -y && \
    apt-get install -y vim git zip unzip libpq-dev libxml2-dev zlib1g-dev libzip-dev mariadb-client libjpeg-dev libpng-dev

RUN apt-get install php-dom

RUN apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN printf "\n" | pecl install imagick

RUN pecl install xdebug-2.9.5 \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install pdo pdo_mysql soap bcmath pcntl zip intl gd

RUN a2enmod headers
RUN a2enmod rewrite
RUN a2enmod ssl

#Install Composer.phar
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    chmod +x composer.phar && \
    mv composer.phar /usr/local/bin/composer

# Install Node.js - uncomment your target version
#RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
#RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
#RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs
RUN echo "NODE Version:" && node --version
RUN echo "NPM Version:" && npm --version
RUN npm i -g nodemon
RUN echo "NODEMON Version:" && nodemon --version

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY vhost-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY php.ini /usr/local/etc/php/php.ini
COPY ssl.crt /etc/apache2/ssl/ssl.crt
COPY ssl.key /etc/apache2/ssl/ssl.key

RUN a2ensite default-ssl.conf
