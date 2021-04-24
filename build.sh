#!/usr/bin/env bash

BOX=.box.phar
[[ ! -f $BOX ]] && \
  wget -O$BOX https://github.com/box-project/box/releases/download/3.12.2/box.phar

echo "Building phar" >&2
chmod +x $BOX
./$BOX compile