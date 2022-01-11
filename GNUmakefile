JS_SOURCES := $(shell find plugin/src/Resources/app/administration -type f)

all : plugin/src/Resources/public/administration/js/kibo-pricemotion.js
.PHONY : all

clean :
	rm -rf plugin/src/Resources/public
.PHONY : clean

plugin/src/Resources/public/administration/js/kibo-pricemotion.js : node_modules $(JS_SOURCES)
	node_modules/.bin/esbuild plugin/src/Resources/app/administration/src/main.js --bundle --loader:.twig=text --outfile=$@

node_modules : yarn.lock
	yarn || touch -d@0 $@
