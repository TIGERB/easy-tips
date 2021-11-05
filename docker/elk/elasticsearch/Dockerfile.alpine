FROM alpine:3.12.0

ENV JAVA_HOME=/usr/lib/jvm/java-11-openjdk

RUN apk update \
    && apk --no-cache add perl-utils bash \
    && adduser -D elasticsearch \
    && apk --no-cache add openjdk11 --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community \
    && wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.9.2-linux-x86_64.tar.gz \
    && wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 \
    && shasum -a 512 -c elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 \
    && tar -xzf elasticsearch-7.9.2-linux-x86_64.tar.gz \
    && chown -R elasticsearch:elasticsearch /elasticsearch-7.9.2 \
    && rm -rf elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 elasticsearch-7.9.2-linux-x86_64.tar.gz

# USER elasticsearch

# ENTRYPOINT ["/elasticsearch-7.9.2/bin/elasticsearch"]

# adduser -D elasticsearch

# apk --no-cache add openjdk11 --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community
# export JAVA_HOME=/usr/lib/jvm/java-11-openjdk

# apk --no-cache add shasum bash

# wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.9.2-linux-x86_64.tar.gz
# wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512
# shasum -a 512 -c elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 
# tar -xzf elasticsearch-7.9.2-linux-x86_64.tar.gz
# cd elasticsearch-7.9.2/


# ---
# wget -q -O /etc/apk/keys/sgerrand.rsa.pub https://alpine-pkgs.sgerrand.com/sgerrand.rsa.pub
# wget https://github.com/sgerrand/alpine-pkg-glibc/releases/download/2.29-r0/glibc-2.29-r0.apk
# apk add glibc-2.29-r0.apk