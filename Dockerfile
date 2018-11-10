FROM webdevops/php-apache-dev:ubuntu-18.04

MAINTAINER kianda

USER root

RUN apt-get -y update
RUN apt-get -y install netcat-traditional

COPY app/ /app/

# No need anything else, it's already all inside webdevops/php-apache-dev:ubuntu-18.04