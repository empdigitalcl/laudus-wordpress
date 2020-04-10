# Copyright 2017 Google Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

# Dockerfile for PHP 5.6/7.0/7.1 using nginx as the webserver.

# Dockerfile.yaml.
# Por: ricardo@empdigital.cl
# Dockerfile basado en configuración de google, tanto para se usando en entorno local
# como para se utilizado en Google App Engine entorno FLEX.

FROM gcr.io/google-appengine/php-base:2019-03-04-13-24
ENV DOCUMENT_ROOT=/app/public
ARG PHP_VERSION=7.2

RUN /bin/bash /build-scripts/install_php.sh && \
    #sed -i -e 's/^disable_functions.*/disable_functions =/g' /opt/php72/lib/php.ini && \
    /bin/bash /build-scripts/install_composer.sh && \
    chgrp www-data /build-scripts/detect_php_version.php && \
    # Temporary enable the shell for www-data
    # We will disable it in lockdown.sh
    chsh -s /bin/bash www-data && \
      cd /build-scripts && \
      su www-data -c "php /usr/local/bin/composer require composer/semver"

# Instala bcmath, para utilización de Telescope.
RUN echo "deb http://cz.archive.ubuntu.com/ubuntu cosmic main universe" >> /etc/apt/sources.list
RUN apt-get update && \
    apt-get install --no-install-recommends --no-install-suggests --yes make && \
    apt-get install --no-install-recommends --no-install-suggests --yes vim && \
    apt-get install --no-install-recommends --no-install-suggests --yes nano && \
    apt-get install --no-install-recommends --no-install-suggests --yes iproute2

RUN apt-get install --no-install-recommends --no-install-suggests --yes php7.2-bcmath
RUN apt-get install --no-install-recommends --no-install-suggests --yes php7.2-dev
RUN apt-get install --no-install-recommends --no-install-suggests --yes php7.2-xdebug

RUN cp /usr/share/php7.2-bcmath/bcmath/bcmath.ini /opt/php72/lib/ext.available/ext-bcmath.ini && \
	ln -s /opt/php72/lib/ext.available/ext-bcmath.ini /opt/php72/lib/ext.enabled/

RUN cp /etc/php/7.2/mods-available/xdebug.ini /opt/php72/lib/ext.available/ext-xdebug.ini && \
  ln -s /opt/php72/lib/ext.available/ext-xdebug.ini /opt/php72/lib/ext.enabled/

RUN echo "xdebug.remote_enable=on" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo "xdebug.remote_autostart=on" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo "xdebug.remote_port=9090" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo "xdebug.remote_handler=dbgp" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo "xdebug.remote_connect_back=0" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo "xdebug.idekey=VSCODE" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo "xdebug.remote_host=172.18.0.1" >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo 'xdebug.remote_mode=req' >> /opt/php72/lib/ext.available/ext-xdebug.ini \
  && echo 'xdebug.remote_log="/tmp/xdebug.log"' >> /opt/php72/lib/ext.available/ext-xdebug.ini

RUN ln -s /usr/lib/php/20170718/xdebug.so /opt/php72/lib/x86_64-linux-gnu/extensions/no-debug-non-zts-20170718/xdebug.so

ADD laravel/ /app/
RUN chmod -R a+r /app

RUN chmod -R 777 storage/

# Agregando directorio para subir archivos y asignando permisos.
RUN mkdir -p /app/public/uploads
RUN chmod 777 /app/public/uploads

RUN composer install
#RUN php artisan telescope:install
RUN php artisan config:clear
RUN php artisan cache:clear
#RUN php artisan key:generate
#RUN php artisan cache:config
#RUN php artisan route:cache
# La migración se debe realizar en forma manual.
#RUN php artisan:migrate

# Agregando entrada DNS host.docker.internal para direccionar facilmente al host.
RUN ip -4 route list match 0/0 | awk '{print $3 "\thost.docker.internal"}' >> /etc/hosts

# Ejecución de scripts php laravel para setting up automático del docker.
# RUN php artisan migrate:install
# RUN php artisan migrate:reset
# RUN php artisan migrate
# RUN php artisan passport:install
# RUN composer dump-autoload
# RUN php artisan db:seed
# RUN php artisan db:seed --class=AvatarSeeder


