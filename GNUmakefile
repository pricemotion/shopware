JS_TARGET := plugin/src/Resources/public/administration/js/kibo-pricemotion.js
JS_SOURCES := $(shell find plugin/src/Resources/app/administration -type f)

all : $(JS_TARGET) plugin/vendor
.PHONY : all

watch :
	rm -f $(JS_TARGET)
	make ESBUILD_ARGS=--watch $(JS_TARGET)
.PHONY : watch

clean :
	rm -rf plugin/src/Resources/public
	rm -rf plugin/composer.lock plugin/vendor
.PHONY : clean

plugin/vendor :
	rm -f plugin/composer.lock
	composer install --no-autoloader --ignore-platform-reqs --working-dir=plugin

$(JS_TARGET) : node_modules $(JS_SOURCES)
	node_modules/.bin/esbuild plugin/src/Resources/app/administration/src/main.js \
		--bundle \
		--loader:.twig=text \
		--outfile=$@ \
		$(ESBUILD_ARGS)

node_modules : yarn.lock
	yarn || touch -d@0 $@
