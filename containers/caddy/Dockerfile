FROM alpine:3.4

LABEL caddy_version="0.9.1" architecture="amd64"

RUN apk add --update --no-cache tar curl

RUN curl --silent --show-error --fail --location \
    --header "Accept: application/tar+gzip, application/x-gzip, application/octet-stream" -o - \
    "https://caddyserver.com/download/build?os=linux&arch=amd64&features=git" \
    | tar --no-same-owner -C /usr/bin/ -xz caddy \
    && chmod 0755 /usr/bin/caddy \
    && /usr/bin/caddy -version

EXPOSE 80 443 2015

VOLUME /var/www/html
WORKDIR /var/www/html

ADD Caddyfile /etc/Caddyfile

ENTRYPOINT ["/usr/bin/caddy"]
CMD ["--conf", "/etc/Caddyfile"]
