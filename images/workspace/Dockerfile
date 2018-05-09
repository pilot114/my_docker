# https://github.com/laradock/laradock/blob/master/workspace/Dockerfile-72

# Здесь уже установлено основное: php-cli, composer, git
FROM laradock/workspace:2.0-72

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
RUN echo workspace:workspace | chpasswd
RUN apt-get install sudo && adduser workspace sudo

RUN apt-get update && apt-get upgrade -y

RUN apt-get install -y zsh

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
#    && chown -R workspace:workspace /home/workspace/.ssh

# TODO для баз клиентов накидать
RUN pecl install mongodb && echo 'extension=mongodb.so' >> /etc/php/7.2/cli/php.ini

USER workspace
RUN curl -fsSL https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh | zsh || true

# add ./vendor/bin to non-root user's bashrc (needed for phpunit)
RUN echo "" >> ~/.bashrc && echo 'export PATH="/var/www/vendor/bin:$PATH"' >> ~/.bashrc

# Change to root
USER root

# Node + yarn
RUN groupadd --gid 1001 node && \
useradd --uid 1001 --gid node --shell /bin/bash --create-home node

RUN set -ex \
  && for key in \
    94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
    FD3A5288F042B6850C66B31F09FE44734EB7990E \
    71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
    DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
    C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
    B9AE9905FFD7803F25714661B63B535A4C206CA9 \
    56730D5401028683275BD23C23EFEFE93C4CFFFE \
    77984A986EBC2AA786BC0F66B01FBB92821C587A \
  ; do \
    gpg --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys "$key" || \
    gpg --keyserver hkp://ipv4.pool.sks-keyservers.net --recv-keys "$key" || \
    gpg --keyserver hkp://pgp.mit.edu:80 --recv-keys "$key" ; \
  done

ENV NODE_VERSION 9.8.0

RUN ARCH= && dpkgArch="$(dpkg --print-architecture)" \
  && case "${dpkgArch##*-}" in \
    amd64) ARCH='x64';; \
    ppc64el) ARCH='ppc64le';; \
    s390x) ARCH='s390x';; \
    arm64) ARCH='arm64';; \
    armhf) ARCH='armv7l';; \
    i386) ARCH='x86';; \
    *) echo "unsupported architecture"; exit 1 ;; \
  esac \
  && curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-$ARCH.tar.xz" \
  && curl -SLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
  && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
  && grep " node-v$NODE_VERSION-linux-$ARCH.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
  && tar -xJf "node-v$NODE_VERSION-linux-$ARCH.tar.xz" -C /usr/local --strip-components=1 --no-same-owner \
  && rm "node-v$NODE_VERSION-linux-$ARCH.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
  && ln -s /usr/local/bin/node /usr/local/bin/nodejs

ENV YARN_VERSION 1.5.1

RUN set -ex \
  && for key in \
    6A010C5166006599AA17F08146C2130DFD2497F5 \
  ; do \
    gpg --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys "$key" || \
    gpg --keyserver hkp://ipv4.pool.sks-keyservers.net --recv-keys "$key" || \
    gpg --keyserver hkp://pgp.mit.edu:80 --recv-keys "$key" ; \
  done \
  && curl -fSLO --compressed "https://yarnpkg.com/downloads/$YARN_VERSION/yarn-v$YARN_VERSION.tar.gz" \
  && curl -fSLO --compressed "https://yarnpkg.com/downloads/$YARN_VERSION/yarn-v$YARN_VERSION.tar.gz.asc" \
  && gpg --batch --verify yarn-v$YARN_VERSION.tar.gz.asc yarn-v$YARN_VERSION.tar.gz \
  && mkdir -p /opt/yarn \
  && tar -xzf yarn-v$YARN_VERSION.tar.gz -C /opt/yarn --strip-components=1 \
  && ln -s /opt/yarn/bin/yarn /usr/local/bin/yarn \
  && ln -s /opt/yarn/bin/yarn /usr/local/bin/yarnpkg \
  && rm yarn-v$YARN_VERSION.tar.gz.asc yarn-v$YARN_VERSION.tar.gz

# TODO: cron
#COPY ./crontab /etc/cron.d
#RUN chmod -R 644 /etc/cron.d

# clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www