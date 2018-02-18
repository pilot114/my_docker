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
	@echo "  nginx v=PATH"
	@echo "  grafana"

build:
	@docker build -t base-alpine-php images/alpine/php
	@docker build -t base-alpine-composer images/alpine/composer
	@docker build -t base-alpine-go images/alpine/golang
	@docker build -t base-nginx images/nginx
	@docker build -t base-grafana images/grafana

php:
	@docker run --rm --name my-alpine-php \
	-v ${ROOT_DIR}/$(v):/usr/src/myapp \
	base-alpine-php

composer:
	@echo ${ROOT_DIR}/$(v)
	@mkdir ${ROOT_DIR}/cache/composer
	@docker run --rm --interactive --tty -v ${ROOT_DIR}/$(v):/app \
	-v ${ROOT_DIR}/cache/composer:/tmp \
	--user $(id -u):$(id -g) \
	base-alpine-composer install --ignore-platform-reqs --no-scripts

go:
	@docker run --rm --name my-alpine-go \
	-v ${ROOT_DIR}/$(v):/usr/src/myapp \
	-w /usr/src/myapp \
	base-alpine-go go build -v

nginx:
	@echo ${ROOT_DIR}/$(v)
	@docker run --rm --name my-nginx -d -p 8080:80 \
	-v ${ROOT_DIR}/$(v):/usr/share/nginx/html \
	base-nginx

grafana:
	@docker run --rm --name my-grafana -d -p 3000:3000 \
	base-grafana


project:
	@echo 'coming soon'
