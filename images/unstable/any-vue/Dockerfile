FROM pilot114/workspace73

#################################################################################
#  install JAVA (open-jdk)
#  https://github.com/docker-library/openjdk/blob/master/12/jdk/oracle/Dockerfile
#################################################################################

# Default to UTF-8 file.encoding
ENV LANG en_US.UTF-8

# need download from https://www.oracle.com/technetwork/java/javase/downloads/jdk11-downloads-5066655.html
COPY jdk-11.0.6_linux-x64_bin.tar.gz /var/cache/oracle-jdk11-installer-local/

RUN echo "oracle-java11-installer-local shared/accepted-oracle-license-v1-2 select true" | debconf-set-selections
RUN add-apt-repository ppa:linuxuprising/java && apt-get update -y

RUN apt install oracle-java11-installer-local -y && \
    apt install oracle-java11-set-default-local -y

ENV JAVA_HOME /usr/lib/jvm/java-11-oracle

#################################################################################
#  install SDK Platform Android 8.0.0, API 26
#  https://github.com/beevelop/docker-android/blob/master/Dockerfile
#################################################################################

ENV ANDROID_SDK_URL="https://dl.google.com/android/repository/tools_r25.2.5-linux.zip" \
    ANDROID_BUILD_TOOLS_VERSION=27.0.0 \
    ANDROID_APIS="android-25,android-26" \
    ANT_HOME="/usr/share/ant" \
    MAVEN_HOME="/usr/share/maven" \
    GRADLE_HOME="/usr/share/gradle" \
    ANDROID_HOME="/opt/android"

ENV PATH $PATH:$ANDROID_HOME/tools:$ANDROID_HOME/platform-tools:$ANDROID_HOME/build-tools/$ANDROID_BUILD_TOOLS_VERSION:$ANT_HOME/bin:$MAVEN_HOME/bin:$GRADLE_HOME/bin

WORKDIR /opt

RUN dpkg --add-architecture i386 && \
    apt-get -qq update && \
    apt-get -qq install -y wget curl maven ant gradle libncurses5:i386 libstdc++6:i386 zlib1g:i386 && \
    # Installs Android SDK
    mkdir android && cd android && \
    wget -O tools.zip ${ANDROID_SDK_URL} && \
    unzip tools.zip && rm tools.zip && \
    echo y | android update sdk -a -u -t platform-tools,${ANDROID_APIS},build-tools-${ANDROID_BUILD_TOOLS_VERSION} && \
    chmod a+x -R $ANDROID_HOME && \
    chown -R root:root $ANDROID_HOME && \
    # Clean up
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    apt-get autoremove -y && \
    apt-get clean

#################################################################################
#  install Cordova
#  https://github.com/beevelop/docker-cordova/blob/master/Dockerfile
#################################################################################

ENV CORDOVA_VERSION 8.0.0
WORKDIR "/tmp"
RUN npm i -g --unsafe-perm cordova@${CORDOVA_VERSION}

#################################################################################
#  install wine and libs for run electron builds (electron-packager)
#################################################################################

# Это всё говнина

#RUN apt-get -qq update && \
#    npm install electron-packager -g
#USER workspace
#RUN yarn global add @vue/cli-init
#USER root
#
#RUN apt-get -qq install -y libgtk-3-0 libx11-xcb1 libxss1 libgconf-2-4
#
#RUN wget -qO - https://dl.winehq.org/wine-builds/winehq.key | sudo apt-key add - && \
#    sudo apt-add-repository 'deb https://dl.winehq.org/wine-builds/ubuntu/ bionic main' && apt-get update && \
#    apt-get install -y --install-recommends winehq-stable && wine --version
#
#RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
#        apt-get autoremove -y && \
#        apt-get clean
#WORKDIR "/app"
#
## java overide to 8 version - for cordova  (((((
#COPY jdk-8u221-linux-x64.tar.gz /tmp
#RUN cd /usr/lib/jvm && tar -xvzf /tmp/jdk-8u221-linux-x64.tar.gz
#RUN rm -R /usr/lib/jvm/java-11-oracle && \
#    update-alternatives --install /usr/bin/java java /usr/lib/jvm/jdk1.8.0_221/bin/java 100 && \
#    update-alternatives --install /usr/bin/javac javac /usr/lib/jvm/jdk1.8.0_221/bin/javac 100
#
#ENV JAVA_HOME /usr/lib/jvm/jdk1.8.0_221
