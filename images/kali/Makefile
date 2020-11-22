#include .env

run:
	@docker run -it --rm -v ${PWD}/x:/x -w /x -p 8080:8080 pilot114/kali zsh
build:
	@docker build -t pilot114/kali .
chrome:
	@docker run -d -p 3000:3000 browserless/chrome
