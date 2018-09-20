ARG image_type=php-apache
 
FROM webdevops/$image_type:alpine-php7
 
LABEL Maintainer "Marek Urban <mail@marekurban.de>"

ENV WEB_DOCUMENT_ROOT=/app/public

ADD ./build/dp-entrypoint.sh /
ADD ./build/inst_temgo.sh /tmp

RUN apk --quiet --no-cache --no-progress add jq \
    && bash /tmp/inst_temgo.sh

RUN chown -R application:apache /app

USER application

RUN composer create-project slim/slim-skeleton /app

ADD --chown=application:apache app/src /app/src
ADD --chown=application:apache app/composer.json /app/composer.json

VOLUME ["/app/download"]

WORKDIR /app

RUN composer install

USER root

# Overwrite default entrypoint to set config from environment. Then the default
# entrypoint is called.
ENTRYPOINT [ "/dp-entrypoint.sh" ]
# "supervisord" must be the first argument. Otherwise nothing gets started.
# This somehow got lost. Without it nothing works
CMD [ "supervisord" ]
