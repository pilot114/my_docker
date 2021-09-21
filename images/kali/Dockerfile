FROM kalilinux/kali-rolling

MAINTAINER Oleg Chulkov pilot114@bk.ru

RUN apt-get update
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

RUN apt-get install -y -q kali-linux-default
RUN apt-get install -y -q nano htop

# fix masscan: issue #144
WORKDIR /root
RUN apt-get install -y git gcc make libpcap-dev
RUN git clone https://github.com/clesiorki2018/masscan && cd masscan && make && cp bin/masscan /usr/bin/
