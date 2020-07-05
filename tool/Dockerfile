FROM php:7.4-cli
RUN apt-get update
RUN apt-get -y install apt-transport-https ca-certificates curl gpgv software-properties-common git unzip
RUN apt-get -y install gnupg2
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add -
RUN add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable"
RUN apt-get update
RUN apt-get -y install docker-ce

RUN echo $(which docker)
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./src /app
RUN chmod -R +x /app/scripts
RUN /usr/bin/composer install -d /app

WORKDIR /app
ENTRYPOINT ["php","/app/bin/cli.php"]
