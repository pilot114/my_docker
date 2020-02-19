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
	@docker build -t pilot114/nginx           images/nginx
	@docker build -t pilot114/grafana         images/grafana
	@docker build -t pilot114/php-wshell      images/php-wshell
	@docker build -t pilot114/workspace       images/workspace
	@docker build -t pilot114/workspace72     images/workspace72
	@docker build -t pilot114/workspace73     images/workspace73
	@docker build -t pilot114/nexus           images/nexus
	@docker build -t pilot114/stream-vk       images/stream-vk
	@docker build -t pilot114/any-vue         images/any-vue

go:
	@docker run -it -v $(v):/app -w /app golang bash

nginx:
	@docker run --rm --name my-nginx -d -p 8080:80 \
	-v $(v):/usr/share/nginx/html \
	pilot114/nginx

grafana:
	@docker run --rm --name my-grafana -d -p 3000:3000 \
	pilot114/grafana

postgres:
	@docker run --name some-postgres -e POSTGRES_PASSWORD=mysecretpassword -d -p 5432:5432 postgres

export:
	@mkdir $(d)
	@docker save pilot114/nginx > $(d)/nginx.tar
	@docker save pilot114/grafana > $(d)/grafana.tar
	@docker save pilot114/php-wshell > $(d)/php-wshell.tar
	@docker save pilot114/workspace > $(d)/workspace.tar
	@docker save pilot114/workspace72 > $(d)/workspace72.tar
	@docker save pilot114/workspace72 > $(d)/workspace73.tar
	@docker save pilot114/nexus > $(d)/nexus.tar
	@docker save pilot114/stream-vk > $(d)/stream-vk.tar
	@docker save pilot114/any-vue > $(d)/any-vue.tar

import:
	@docker load < $(d)/nginx.tar
	@docker load < $(d)/grafana.tar
	@docker load < $(d)/php-wshell.tar
	@docker load < $(d)/workspace.tar
	@docker load < $(d)/workspace72.tar
	@docker load < $(d)/workspace73.tar
	@docker load < $(d)/nexus.tar
	@docker load < $(d)/stream-vk.tar
	@docker load < $(d)/any-vue.tar

prune:
	@docker stop `docker ps -a -q` && docker system prune

push:
	@docker push pilot114/nginx
	@docker push pilot114/grafana
	@docker push pilot114/php-wshell
	@docker push pilot114/workspace
	@docker push pilot114/workspace72
	@docker push pilot114/workspace73
	@docker push pilot114/nexus
	@docker push pilot114/stream-vk
	@docker push pilot114/any-vue
