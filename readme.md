### Overview

Docker образы на все случаи жизни.
Запилил для себя, в основном чтобы быстро разворачивать понравившиеся проекты
с GitHub.

* images/alpine:
    * php - базовый php для микросервисов
    * composer - для установки vendor

Все типовые сборки и запуск сервисов осуществляются через make

Примеры:

    # сбилдить все образы
    make build

    # просто вывести версию php и список модулей для alpine php
    make micro-php v=volumes/php_script

    # установить зависимости в директорию проекта (используется cache)
    make composer v=volumes/php_project

### TODO - подумать как интегрировать всяческие метасервисы

mattermost - типа Slack, gogs - типа GitHub и пр.

    # Pull image from Docker Hub.
    $ docker pull gogs/gogs
    
    # Create local directory for volume.
    $ mkdir -p /var/gogs
    
    # Use `docker run` for the first time.
    $ docker run --name=gogs -p 10022:22 -p 10080:3000 -v /var/gogs:/data gogs/gogs
    
    # Use `docker start` if you have stopped it.
    $ docker start gogs

### Заметки

makefile должен содержать tab вместо space, так работает make