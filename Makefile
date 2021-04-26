DOCKER_COMPOSE_BIN := docker-compose
BOX=.box.phar
$(BOX):
	wget -q https://github.com/box-project/box/releases/download/3.12.2/box.phar -O$(BOX)

clean:
	rm -rf ./dist/*

build-phar: $(BOX)
	echo "Compiling"
	chmod +x $(BOX)
	./$(BOX) compile

build-image: clean build-phar
	docker build --no-cache -t myshoppress/tmpl:$(VER) -f dockerfile-phar .
	docker run --rm -it -v $(shell pwd)/examples:/app myshoppress/tmpl "-f nginx.conf.hbs"

release: build
	gh release create $(VER) ./dist/tmpl -t "Release $(VER)"  -p

build-dep:
	@$(DOCKER_COMPOSE_BIN) run --rm php composer update


test:
	@$(DOCKER_COMPOSE_BIN) run --rm php "composer run test"

