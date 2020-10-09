help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"

	@echo "  build"
	@echo "  enter"
	@echo "  run"
	@echo "  go_run"

enter:
	@docker run -it --rm --name task_dealer_instance pilot114/task_dealer sh
run:
	@docker run -it --rm -p 8080:8080 -v $(PWD)/data:/data --name task_dealer_instance pilot114/task_dealer

go_run:
	@cd app && go run app.go
