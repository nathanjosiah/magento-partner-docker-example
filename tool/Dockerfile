FROM docker
COPY ./script.sh /script.sh
RUN chmod +x /script.sh
COPY ./composer.json /
COPY ./composer.lock /

ENTRYPOINT ["sh","/script.sh"]
