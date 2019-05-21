FROM ubuntu:18.04

RUN apt-get update \
    && apt-get -y install git-core \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN mkdir -p /subsplit \
    && cd /subsplit \
    && git clone https://github.com/dflydev/git-subsplit.git . \
    && git checkout 2cde593a10acdb3fcafd2136ee7025fee9e925b2 \
    && ./install.sh