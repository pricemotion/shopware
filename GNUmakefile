format : node_modules
	git ls-files -z | xargs -0r node_modules/.bin/prettier --write --ignore-unknown
.PHONY : format

node_modules : yarn.lock
	yarn
	touch $@
