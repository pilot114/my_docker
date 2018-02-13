include .env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  build"
	@echo "  php v=PATH"
	@echo "  composer v=PATH"
	@echo "  go v=PATH"

build:
	@docker build -t alpine-php images/alpine/php
	@docker build -t alpine-composer images/alpine/composer
	@docker build -t alpine-go images/alpine/golang

php:
	@docker run --rm --name my-alpine-php \
	-v ${ROOT_DIR}/$(v):/usr/src/myapp \
	alpine-php

composer:
	@echo ${ROOT_DIR}/$(v)
	@docker run --rm --interactive --tty -v ${ROOT_DIR}/$(v):/app \
	-v ${COMPOSER_HOME}:/tmp \
	--user $(id -u):$(id -g) \
	alpine-composer install --ignore-platform-reqs --no-scripts

go:
	@docker run --rm --name my-alpine-go \
	-v ${ROOT_DIR}/$(v):/usr/src/myapp \
	-w /usr/src/myapp \
	alpine-go go build -v
