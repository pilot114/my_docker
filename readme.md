### Overview

Docker образы на (почти) все случаи жизни

* images - для типичных юзкейсов
    * alpine
        * php - базовый php для микросервисов
        * composer - для установки vendor
        * golang - для компиляции golang проектов
    * nginx - базовый образ для http/https сервисов
    * grafana - мониторинг

* volumes - файлы для примеров типового использования
* composes - сборки для конкретных проектов

TODO: настроить nginx так, чтобы можно было просто пробросить
папку с конкретными конфигами

Все типовые сборки и запуск сервисов осуществляются через make

Примеры:

    # сбилдить все образы в images
    make build

    # просто запустить index.php
    make php v=volumes/php_script

    # установить зависимости в директорию проекта (используется cache)
    make composer v=volumes/php_project
    
    # скомпилировать проект на go
    make go v=volumes/go_project

    # раздать статику на 8080 порту
    make nginx v=volumes/html

    # запустить сервис мониторинга 3000 порту
    make grafana
    
### Заметки

makefile должен содержать tab вместо space, так работает make