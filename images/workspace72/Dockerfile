# Образ "всё-в-одном", для web-разработки в стеке PHP + JS
# (https://phusion.github.io/baseimage-docker/)
#
# Основан на phusion/baseimage-docker (ubuntu:18.04 - поддержка до 23 года), что означает:
# - все базовые службы ОС работают (init, syslog, cron, ssh daemon)
# - образ "съедает" всего 6Mb RAM
# - можно добавлять своих демонов

FROM phusion/baseimage:0.11

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

RUN apt-get update && \
    apt-get install -y --allow-downgrades --allow-remove-essential \
        --allow-change-held-packages \
        php7.2-cli \
        php7.2-common \
        php7.2-curl \
        php7.2-intl \
        php7.2-json \
        php7.2-xml \
        php7.2-mbstring \
        php7.2-mysql \
        php7.2-pgsql \
        php7.2-sqlite \
        php7.2-sqlite3 \
        php7.2-zip \
        php7.2-bcmath \
        php7.2-memcached \
        php7.2-gd \
        php7.2-dev \
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
        htop

# обновляем кэш pecl
RUN pecl channel-update pecl.php.net
RUN pecl install mongodb && echo 'extension=mongodb.so' >> /etc/php/7.2/cli/php.ini

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
# TODO: добавить алисы для git, когда достаточно хорошо пойму что нужно
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
RUN apt-get install -y wget && wget -qO- https://deb.nodesource.com/setup_8.x | sudo -E bash - && \
    apt-get install -y nodejs build-essential

# global codecept, phpunit, webpack, phpmd
RUN curl -LsS https://codeception.com/codecept.phar -o /usr/local/bin/codecept && \
    chmod a+x /usr/local/bin/codecept
RUN wget https://phar.phpunit.de/phpunit-6.phar -O /usr/local/bin/phpunit && \
    chmod a+x /usr/local/bin/phpunit
RUN npm install --global webpack webpack-cli
RUN wget -c http://static.phpmd.org/php/latest/phpmd.phar -O /usr/local/bin/phpmd && \
    chmod a+x /usr/local/bin/phpmd

# install vue-cli
RUN npm install -g @vue/cli

# install yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list && \
    sudo apt-get install -y yarn

# Очистка
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

WORKDIR /var/www

# корректный запуск init c переключением на workspace
# CMD ["/sbin/my_init", "--", "setuser", "workspace", "zsh"]
# upd.: это не заставляет докер запускаться под workspace.
# Видимо, в любом случае, придется передавать -u workspace при старте