# https://github.com/laradock/laradock/blob/master/workspace/Dockerfile-70

# Здесь уже установлено основное: php-cli, composer, git
FROM laradock/workspace:2.0-70

# чистим логи
RUN rm /var/log/lastlog /var/log/faillog

# таймзона
ARG TZ=UTC
ENV TZ ${TZ}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# cron
COPY ./crontab /etc/cron.d
RUN chmod -R 644 /etc/cron.d

# Уходим из под юзера root на нового юзера workspace (тем не менее, имеет sudo)
# Можно передать иды пользователя с хоста

ARG PUID=1000
ARG PGID=1000
ENV PUID ${PUID}
ENV PGID ${PGID}

RUN groupadd -g ${PGID} workspace && \
    useradd -u ${PUID} -g workspace -m workspace && \
    apt-get update -yqq
RUN echo workspace:workspace | chpasswd
RUN apt-get install sudo && adduser workspace sudo

# TODO переварить это https://github.com/laradock/laradock/blob/master/workspace/aliases.sh
#COPY ./aliases.sh /home/workspace/aliases.sh
#RUN echo "" >> ~/.bashrc && \
#   echo "# Кастомные алиасы" >> ~/.bashrc && \
#   echo "source /home/workspace/aliases.sh" >> ~/.bashrc && \
#	echo "" >> ~/.bashrc && \
#	sed -i 's/\r//' /home/workspace/aliases.sh && \
#	sed -i 's/^#! \/bin\/sh/#! \/bin\/bash/' /home/workspace/aliases.sh

# TODO ssh
#ADD insecure_id_rsa /tmp/id_rsa
#ADD insecure_id_rsa.pub /tmp/id_rsa.pub
#RUN rm -f /etc/service/sshd/down && \
#    cat /tmp/id_rsa.pub >> /root/.ssh/authorized_keys \
#        && cat /tmp/id_rsa.pub >> /root/.ssh/id_rsa.pub \
#        && cat /tmp/id_rsa >> /root/.ssh/id_rsa \
#        && rm -f /tmp/id_rsa* \
#        && chmod 644 /root/.ssh/authorized_keys /root/.ssh/id_rsa.pub \
#    && chmod 400 /root/.ssh/id_rsa \
#    && cp -rf /root/.ssh /home/laradock \
#    && chown -R laradock:laradock /home/laradock/.ssh

# TODO npm и yarn
# TODO для баз клиентов накидать

RUN apt-get update && apt-get upgrade -y

RUN apt-get install -y zsh

USER workspace
RUN curl -fsSL https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh | zsh || true

# add ./vendor/bin to non-root user's bashrc (needed for phpunit)
RUN echo "" >> ~/.bashrc && echo 'export PATH="/var/www/vendor/bin:$PATH"' >> ~/.bashrc

# Clean up
USER root
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www