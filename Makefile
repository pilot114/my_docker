include .env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  build"
	@echo "  micro-php v=PATH"
	@echo "  composer v=PATH"
	@echo "  go-compile v=PATH"

build:
	@docker build -t micro-php images/alpine/php
	@docker build -t micro-composer images/alpine/composer
	@docker build -t micro-go images/alpine/golang

micro-php:
	@docker run -it --rm --name my-micro-php -v ${ROOT_DIR}/$(v):/usr/src/myapp micro-php

composer:
	@echo ${ROOT_DIR}/$(v)
	@docker run --rm --interactive --tty -v ${ROOT_DIR}/$(v):/app \
	--volume ${COMPOSER_HOME}:/tmp \
	--user $(id -u):$(id -g) \
	composer install --ignore-platform-reqs --no-scripts

go-compile:
	@docker run --rm -v ${ROOT_DIR}/$(v):/usr/src/myapp -w /usr/src/myapp micro-go go build -v
