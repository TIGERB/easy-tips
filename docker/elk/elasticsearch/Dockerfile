FROM centos:centos8

# 内置了java jdk
ENV JAVA_HOME=/home/elasticsearch/elasticsearch-7.9.2/jdk

RUN yum -y update \
    && yum -y install wget perl-Digest-SHA \
    && adduser -d /home/elasticsearch elasticsearch \
    && cd /home/elasticsearch \
    && wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.9.2-linux-x86_64.tar.gz \
    && wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 \
    && shasum -a 512 -c elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 \
    && tar -xzf elasticsearch-7.9.2-linux-x86_64.tar.gz \
    && chown -R elasticsearch:elasticsearch /home/elasticsearch/elasticsearch-7.9.2 \
    && rm -rf ./elasticsearch-7.9.2-linux-x86_64.tar.gz.sha512 ./elasticsearch-7.9.2-linux-x86_64.tar.gz

# ENTRYPOINT ["/home/elasticsearch/elasticsearch-7.9.2/bin/elasticsearch"]