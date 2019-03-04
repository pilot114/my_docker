include .env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  build"
	@echo "  go v=PATH"
	@echo "  nginx v=PATH"
	@echo "  grafana"
	@echo "  export d=backup_images"
	@echo "  import d=backup_images"
	@echo "  prune"
	@echo "  push"
	@echo ""
	@echo "  service_build"
	@echo "  service_push"

build:
	@docker build -t pilot114/base-nginx           images/nginx
	@docker build -t pilot114/base-grafana         images/grafana
	@docker build -t pilot114/base-php-wshell      images/php-wshell
	@docker build -t pilot114/base-workspace       images/workspace
	@docker build -t pilot114/base-workspace72     images/workspace72

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

postgres:
	@docker run --name some-postgres -e POSTGRES_PASSWORD=mysecretpassword -d -p 5432:5432 postgres

export:
	@mkdir $(d)
	@docker save pilot114/base-nginx > $(d)/base-nginx.tar
	@docker save pilot114/base-grafana > $(d)/base-grafana.tar
	@docker save pilot114/base-php-wshell > $(d)/base-php-wshell.tar
	@docker save pilot114/base-workspace > $(d)/base-workspace.tar
	@docker save pilot114/base-workspace72 > $(d)/base-workspace72.tar

import:
	@docker load < $(d)/base-nginx.tar
	@docker load < $(d)/base-grafana.tar
	@docker load < $(d)/base-php-wshell.tar
	@docker load < $(d)/base-workspace.tar
	@docker load < $(d)/base-workspace72.tar

prune:
	@docker stop $(docker ps -a -q) && docker system prune

push:
	@docker push pilot114/base-nginx
	@docker push pilot114/base-grafana
	@docker push pilot114/base-php-wshell
	@docker push pilot114/base-workspace
	@docker push pilot114/base-workspace72

#############################################
# далее - только то, что относится к сервисам
#############################################

service_build:
	@cd services && ./build.sh
service_push:
	@cd services && ./push.sh
