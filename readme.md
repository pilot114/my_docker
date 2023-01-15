### usefully aliases

    PREFIX='docker run --rm -it -v "$PWD":/app -w /app -u $(id -u):$(id -g)'

    alias   node='eval $PREFIX node:19.3.0-alpine3.17 node'
    alias    php='eval $PREFIX --init php:8.2.0-cli-alpine3.17 php' # --init for correct handle ctrl+C
    alias python='eval $PREFIX python:3.11.1-alpine3.17 python'
    alias     go='eval $PREFIX golang:1.19.4-alpine3.17 go'

    alias      npm='eval $PREFIX node:19.3.0-alpine3.17 npm'
    alias      npx='eval $PREFIX node:19.3.0-alpine3.17 npx'
    alias composer='eval $PREFIX composer:2.5.1 composer'
    alias      pip='eval $PREFIX python:3.11.1-alpine3.17 pip'


### Overview

Docker образы на типовый случаи, а также для демок.

Все типовые сборки осуществляются через make

Примеры:

    # сбилдить все образы в images
    make build

    # скомпилировать проект на go
    make go v=$(pwd)/path_to_go_project

    # раздать статику на 8080 порту
    make nginx v=$(pwd)/path_to_files

    # запустить сервис мониторинга на 3000 порту
    make grafana

В качестве параметра 'v' передаётся абсолютный путь до директории.

Рекомендуется заходить в workspace под соответствующим юзером:

    docker exec -it -u workspace CONTAINER_ID zsh

Может оказаться полезным делать экспорт/импорт созданных образов в tar архивы.
Для этого можно использовать команды:

    make export name=backup_images
    make import name=backup_images

Для загрузки изменённых образов на gitHub нужно выполнить:

    make push

можно конечно сделать webhook для загрузки образов по коммиту,
но ручной запуск этого процесса более нагляден.

### Заметки

- makefile должен содержать tab вместо space, так работает make
- утилиту dos2unix можно использовать, чтобы убрать \r символы
- Стоит делать ревизию раз в год для обновления версий базовых образов.

### gitLab

https://docs.gitlab.com/omnibus/docker/
