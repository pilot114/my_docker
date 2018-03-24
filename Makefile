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
	@echo "  export d=backup_images"
	@echo "  import d=backup_images"
	@echo "  "
	@echo "run project/template and enter to workspace:"
	@echo "  wshell"
	@echo "  fileserver"

build:
	@docker build -t base-alpine-php images/alpine/php
	@docker build -t base-alpine-composer images/alpine/composer
	@docker build -t base-alpine-go images/alpine/golang
	@docker build -t base-nginx images/nginx
	@docker build -t base-grafana images/grafana
	@docker build -t base-php-full images/php-full
	@docker build -t base-workspace images/workspace

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

export:
	@mkdir $(d)
	@docker save base-alpine-php > $(d)/base-alpine-php.tar
	@docker save base-alpine-composer > $(d)/base-alpine-composer.tar
	@docker save base-alpine-go > $(d)/base-alpine-go.tar
	@docker save base-nginx > $(d)/base-nginx.tar
	@docker save base-grafana > $(d)/base-grafana.tar
	@docker save base-php-full > $(d)/base-php-full.tar
	@docker save base-workspace > $(d)/base-workspace.tar

import:
	@docker load < $(d)/base-alpine-php.tar
	@docker load < $(d)/base-alpine-composer.tar
	@docker load < $(d)/base-alpine-go.tar
	@docker load < $(d)/base-nginx.tar
	@docker load < $(d)/base-grafana.tar
	@docker load < $(d)/base-php-full.tar
	@docker load < $(d)/base-workspace.tar



wshell:
	@cd composes/wshell && docker-compose up -d
	@docker exec -it -u workspace wshell_workspace_1 zsh
	@cd composes/wshell && docker-compose stop

fileserver:
	@cd composes/fileserver && docker-compose up -d
	@docker exec -it -u workspace fileserver_workspace_1 zsh
	@cd composes/fileserver && docker-compose stop
