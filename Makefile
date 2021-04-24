BOX=.box.phar

$(BOX):
	wget -q https://github.com/box-project/box/releases/download/3.12.2/box.phar -o$(BOX)

build: $(BOX)
	echo "Compiling"
