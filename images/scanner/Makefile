help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"

	@echo "  build"
	@echo "  enter"
	@echo "  run a=INT b=INT w=INT (a - first octet in ip, b - second octet in ip, w - count workers)"
	@echo "  stop"

# зайти в контейнер
enter:
	@docker run -it --rm --name scanner_instance pilot114/scanner sh

# запускаем, лог ошибок и данные выводим в соотвествующие файлы
run:
	@docker run --rm -d --name scanner_instance pilot114/scanner /root/app $(a) $(b) $(w)
	@docker logs -f scanner_instance > output/$(a)_$(b)_$(w).data 2>output/$(a)_$(b)_$(w).log &

stop:
	@docker stop scanner_instance
