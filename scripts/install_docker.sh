#!/usr/bin/env bash

if [ -x "$(command -v docker)" ]; then
  echo 'docker already installed'
  if [ -x "$(command -v docker-compose)" ]; then
    echo 'docker-compose already installed'
    exit 0
  fi
fi

sudo apt-get remove -y docker docker-engine docker.io

sudo apt-get update
sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo apt-key fingerprint 0EBFCD88
sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"

sudo apt-get update
sudo apt-get install -y docker-ce

groupadd docker
usermod -aG docker $USER

sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose