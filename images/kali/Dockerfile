FROM kalilinux/kali-rolling

MAINTAINER Oleg Chulkov pilot114@bk.ru

RUN apt-get update
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

RUN apt-get install -y -q nano kali-linux-default
