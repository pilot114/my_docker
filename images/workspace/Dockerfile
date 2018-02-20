# Здесь уже установлено основное: php-cli, composer, git
FROM laradock/workspace:2.0-70

# чистим логи
RUN rm /var/log/lastlog /var/log/faillog

# таймзона
ARG TZ=UTC
ENV TZ ${TZ}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Уходим из под юзера root на нового юзера workspace (тем не менее, имеет sudo)
# Можно передать иды пользователя с хоста

ARG PUID=1000
ARG PGID=1000
ENV PUID ${PUID}
ENV PGID ${PGID}

RUN groupadd -g ${PGID} workspace && \
    useradd -u ${PUID} -g workspace -m workspace && \
    apt-get update -yqq
# TODO: не работает как надо
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

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

USER workspace

# oh my zsh!
RUN sudo apt-get install -y zsh
RUN sudo wget https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | zsh || true

WORKDIR /var/www