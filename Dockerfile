FROM phusion/baseimage

RUN apt-get -qq update
RUN apt-get -qq -y install wget
RUN apt-get -qq -y install unzip
RUN apt-get -qq -y install git
RUN apt-get -qq -y install php libapache2-mod-php php-mcrypt php-mysql
RUN apt-get -qq -y install curl php-cli php-mbstring
RUN apt-get -qq -y install apache2
RUN apt-get -qq -y install php7.0-dom php7.0-phar php7.0-gd php7.0-iconv php7.0-json php7.0-mysql php7.0-xml

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ARG COMPOSER_ALLOW_SUPERUSER=1
ARG COMPOSER_NO_INTERACTION=1

RUN mkdir /var/www/html/elgg/ && mkdir /var/www/elgg_data/
WORKDIR /var/www/html/elgg
RUN git init && git remote add origin https://github.com/Services-Sandbox/Elgg.git && git fetch origin && git pull origin 3.x && chmod -R 777 .
RUN chmod -R 777 /var/www/elgg_data/

RUN composer global require fxp/composer-asset-plugin
RUN composer install

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN a2enmod rewrite


RUN /etc/init.d/apache2 restart

EXPOSE 80

CMD apachectl -D FOREGROUND





