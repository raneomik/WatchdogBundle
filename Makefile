.DEFAULT_GLOBAL = help
SHELL:=/bin/bash

LOW_PHP = 7.4
HIGH_PHP = 8.1
SF = symfony

help:	## Shows this help hint
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'


##---------------------------------------------------------------------------
##
## Tests - Unit/Integration/Application/Mutation & Static code
##
full-test:	## Full bundle test
full-test: check-deps check-code lint infection

check-deps:	## Check php dependencies
	$(SF) composer outdated
	$(SF) composer validate
	$(SF) security:check

check-code:	## Static code analysis
check-code: ecs psalm stan

ecs: ## Code Sniff fixer
	vendor/bin/ecs check src tests --fix

psalm: ## Psalm analysis
	@$(SF) composer req php:^8.0 -q --no-ansi
	$(SF) php vendor/bin/psalm --no-progress --show-info=true --no-cache
	@$(SF) composer req php:"^7.4|^8.0" -q --no-ansi
stan: ## phpstan analysis
	$(SF) php vendor/bin/phpstan --no-progress

lint: ## Config files lint
	vendor/bin/neon-lint .

test: ## Unit tests
ifdef FILTER
	$(SF) php vendor/bin/phpunit --filter $(FILTER)
else
	$(SF) php vendor/bin/phpunit
endif

cover: ## Unit tests with coverage
	XDEBUG_MODE=coverage $(SF) php vendor/bin/simple-phpunit --coverage-xml=cov/xml --coverage-html=cov/html --log-junit=cov/junit.xml

infection: ## Mutation tests
	XDEBUG_MODE=coverage vendor/bin/infection --ansi

##---------------------------------------------------------------------------
##
## Dependencies - highest vs. lowest requirements case switch
##
up-deps: ## Update to latest dependencies
	 $(SF) composer require --no-progress --no-update --no-scripts --dev \
              symplify/easy-coding-standard:* symplify/coding-standard:* symplify/phpstan-rules:* \
              phpstan/phpstan-symfony:* ekino/phpstan-banned-code:* phpstan/phpstan-phpunit:* phpstan/extension-installer:* phpstan/phpstan:* \
              psalm/plugin-symfony:* vimeo/psalm:* \
              infection/infection:*
	echo $(HIGH_PHP) > .php-version
	$(SF) composer update --no-interaction --no-progress -W

down-deps: ## Downgrade to least supported dependencies
	 $(SF) composer remove --no-progress --no-update --no-scripts --dev \
              symplify/* phpstan/* ekino/phpstan-banned-code \
              psalm/plugin-symfony vimeo/psalm \
              infection/infection
	echo $(LOW_PHP) > .php-version
	$(SF) composer update --no-interaction --no-progress --prefer-lowest --prefer-stable -W

