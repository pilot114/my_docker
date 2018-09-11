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
	@echo "  prune"
	@echo "  push"

build:
	@docker build -t pilot114/base-alpine-php      images/alpine/php
	@docker build -t pilot114/base-alpine-composer images/alpine/composer
	@docker build -t pilot114/base-alpine-go       images/alpine/golang
	@docker build -t pilot114/base-nginx           images/nginx
	@docker build -t pilot114/base-grafana         images/grafana
	@docker build -t pilot114/base-php-wshell      images/php-wshell
	@docker build -t pilot114/base-workspace       images/workspace
	@docker build -t pilot114/base-laradock        images/laradock

php:
	@docker run --rm --name my-alpine-php \
	-v $(v):/usr/src/myapp \
	pilot114/base-alpine-php

composer:
	@docker run --rm --interactive --tty -v $(v):/app \
	-v ${ROOT_DIR}/cache/composer:/tmp \
	--user $(id -u):$(id -g) \
	pilot114/base-alpine-composer install --ignore-platform-reqs --no-scripts

go:
	@docker run --rm --name my-alpine-go \
	-v $(v):/usr/src/myapp \
	-w /usr/src/myapp \
	pilot114/base-alpine-go go build -v

nginx:
	@docker run --rm --name my-nginx -d -p 8080:80 \
	-v $(v):/usr/share/nginx/html \
	pilot114/base-nginx

grafana:
	@docker run --rm --name my-grafana -d -p 3000:3000 \
	pilot114/base-grafana

export:
	@mkdir $(d)
	@docker save pilot114/base-alpine-php > $(d)/base-alpine-php.tar
	@docker save pilot114/base-alpine-composer > $(d)/base-alpine-composer.tar
	@docker save pilot114/base-alpine-go > $(d)/base-alpine-go.tar
	@docker save pilot114/base-nginx > $(d)/base-nginx.tar
	@docker save pilot114/base-grafana > $(d)/base-grafana.tar
	@docker save pilot114/base-php-wshell > $(d)/base-php-wshell.tar
	@docker save pilot114/base-workspace > $(d)/base-workspace.tar
	@docker save pilot114/base-laradock > $(d)/base-laradock.tar

import:
	@docker load < $(d)/base-alpine-php.tar
	@docker load < $(d)/base-alpine-composer.tar
	@docker load < $(d)/base-alpine-go.tar
	@docker load < $(d)/base-nginx.tar
	@docker load < $(d)/base-grafana.tar
	@docker load < $(d)/base-php-wshell.tar
	@docker load < $(d)/base-workspace.tar
	@docker load < $(d)/base-laradock.tar

prune:
	@docker stop $(docker ps -a -q) && docker system prune

push:
	@docker push pilot114/base-alpine-php
	@docker push pilot114/base-alpine-composer
	@docker push pilot114/base-alpine-go
	@docker push pilot114/base-nginx
	@docker push pilot114/base-grafana
	@docker push pilot114/base-php-wshell
	@docker push pilot114/base-workspace
	@docker push pilot114/base-laradock
