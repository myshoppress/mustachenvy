BOX=.box.phar
$(BOX):
	wget -q https://github.com/box-project/box/releases/download/3.12.2/box.phar -O$(BOX)

build: $(BOX)
	echo "Compiling"
	chmod +x $(BOX)
	./$(BOX) compile

release: build
	gh release create $(VER) ./dist/tmpl -t "Release $(VER)"  -p
