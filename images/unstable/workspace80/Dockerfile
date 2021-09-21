# Образ "всё-в-одном", для web-разработки в стеке PHP + JS
# (https://phusion.github.io/baseimage-docker/)
#
# Основан на phusion/baseimage-docker (ubuntu:18.04 - поддержка до 23 года), что означает:
# - все базовые службы ОС работают (init, syslog, cron, ssh daemon)
# - образ "съедает" всего 6Mb RAM
# - можно добавлять своих демонов

FROM phusion/baseimage:18.04-1.0.0

MAINTAINER Oleg Chulkov pilot114@bk.ru

# отключаем подтверждения при установке пакетов
RUN DEBIAN_FRONTEND=noninteractive

# локализация
RUN locale-gen ru_RU.UTF-8

ENV LANGUAGE=ru_RU.UTF-8
ENV LC_ALL=ru_RU.UTF-8
ENV LC_CTYPE=ru_RU.UTF-8
ENV LANG=ru_RU.UTF-8

# стандартные настройки эмулятора терминала
ENV TERM xterm

############
# Software #
############
RUN apt-get install -y software-properties-common && \
    add-apt-repository -y ppa:ondrej/php

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

RUN apt-get update && \
    apt-get install -y --allow-downgrades --allow-remove-essential \
        --allow-change-held-packages \
        php8.0-cli \
        php8.0-common \
        php8.0-curl \
        php8.0-intl \
        php8.0-json \
        php8.0-xml \
        php8.0-mbstring \
        php8.0-mysql \
        php8.0-pgsql \
        php8.0-sqlite \
        php8.0-sqlite3 \
        php8.0-zip \
        php8.0-bcmath \
        php8.0-memcached \
        php8.0-gd \
        php8.0-dev \
        pkg-config \
        libcurl4-openssl-dev \
        libedit-dev \
        libssl-dev \
        libxml2-dev \
        xz-utils \
        libsqlite3-dev \
        sqlite3 \
        git \
        curl \
        vim \
        nano \
        postgresql-client \
        mysql-client \
        mongodb-clients \
        zsh \
        tree \
        sudo \
        htop \
        yarn

# обновляем кэш pecl
RUN pecl channel-update pecl.php.net
RUN pecl install mongodb && echo 'extension=mongodb.so' >> /etc/php/8.0/cli/php.ini

# composer
RUN curl -s http://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# таймзона
RUN ln -snf /usr/share/zoneinfo/Asia/Novosibirsk /etc/localtime && echo Asia/Novosibirsk > /etc/timezone

# Кроны
COPY ./crontab /etc/cron.d
RUN chmod -R 644 /etc/cron.d

# Уходим из под юзера root на нового юзера workspace (тем не менее, имеет sudo)
# иды пользователя можно передать с хоста

ARG PUID=1000
ARG PGID=1000
ENV PUID ${PUID}
ENV PGID ${PGID}

RUN groupadd -g ${PGID} workspace && \
    useradd -u ${PUID} -g workspace -m workspace
RUN echo workspace:workspace | chpasswd
RUN adduser workspace sudo

#############################
# Далее настройки workspace #
#############################
USER workspace

# oh-my-zsh
RUN curl -fsSL https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh | zsh || true

# алиасы
COPY ./aliases.sh /home/workspace/aliases.sh

RUN sed -i 's/\r//' /home/workspace/aliases.sh && \
    chown workspace:workspace /home/workspace/aliases.sh && \
    echo "\nsource ~/aliases.sh\n" >> ~/.bashrc && \
    echo "\nsource ~/aliases.sh\n" >> ~/.zshrc

USER root

# ssh ключ по умолчанию
#COPY your_key.pub /tmp/your_key.pub
#RUN cat /tmp/your_key.pub >> /root/.ssh/authorized_keys && rm -f /tmp/your_key.pub
ADD insecure_id_rsa /tmp/id_rsa
ADD insecure_id_rsa.pub /tmp/id_rsa.pub
RUN rm -f /etc/service/sshd/down && mkdir /home/workspace/.ssh && \
    cat /tmp/id_rsa.pub >> /home/workspace/.ssh/authorized_keys \
        && cat /tmp/id_rsa.pub >> /home/workspace/.ssh/id_rsa.pub \
        && cat /tmp/id_rsa >> /home/workspace/.ssh/id_rsa \
        && rm -f /tmp/id_rsa* \
        && chmod 644 /home/workspace/.ssh/authorized_keys /home/workspace/.ssh/id_rsa.pub \
    && chmod 400 /home/workspace/.ssh/id_rsa \
    && chown -R workspace:workspace /home/workspace/.ssh

# install nodejs
RUN apt-get install -y wget && wget -qO- https://deb.nodesource.com/setup_14.x | sudo -E bash - && \
    apt-get install -y nodejs build-essential

RUN npm install --global webpack webpack-cli

# install vue-cli
RUN npm i -g @vue/cli

# Очистка
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

WORKDIR /var/www

# корректный запуск init c переключением на workspace
# CMD ["/sbin/my_init", "--", "setuser", "workspace", "zsh"]
# upd.: это не заставляет докер запускаться под workspace.
# Видимо, в любом случае, придется передавать -u workspace при старте
