include .env

help:
	@echo "Команды (опционально i=IMAGE_NAME):"
	@echo "  install - установка docker + docker_compose"
	@echo "  build   - собрать образы"
	@echo "  push    - загрузить образы на https://hub.docker.com"
	@echo "  prune   - очистить кэш образов"
	@echo ""
	@echo "Сервисы (опционально v=DIR_NAME):"
	@echo "  nginx"
	@echo "  grafana"
	@echo "  postgres"
	@echo "  php8"

install:
	@./scripts/install_docker.sh

build:
	@php -f ./scripts/build.php $(i)

push:
	@php -f ./scripts/push.php $(i)

prune:
	@docker stop `docker ps -a -q` && docker system prune -a

nginx:
	@docker run --rm --name my-nginx -d -p 8080:80 \
	-v $(v):/usr/share/nginx/html \
	pilot114/nginx

grafana:
	@docker run --rm --name my-grafana -d -p 3000:3000 \
	pilot114/grafana

postgres:
	@docker run --name some-postgres -e POSTGRES_PASSWORD=mysecretpassword -d -p 5432:5432 \
	postgres

php8:
	@docker run -it --rm -v ${PWD}/images/php8/examples:/app -w /app \
	pilot114/php8 bash
