LOW_PHP = 7.4
HIGH_PHP = 8.1
SF = symfony

up-deps:
	 $(SF) composer require --no-progress --no-update --no-scripts --dev \
              symplify/coding-standard:* symplify/phpstan-rules:* \
              phpstan/phpstan-symfony:* ekino/phpstan-banned-code:* phpstan/phpstan-phpunit:* phpstan/extension-installer:* phpstan/phpstan:* \
              psalm/plugin-symfony:* vimeo/psalm:* \
              infection/infection:*
	echo $(HIGH_PHP) > .php-version
	$(SF) composer update --no-interaction --no-progress -W

down-deps:
	 $(SF) composer remove --no-progress --no-update --no-scripts --dev \
              symplify/* phpstan/* ekino/phpstan-banned-code \
              psalm/plugin-symfony vimeo/psalm \
              infection/infection
	echo $(LOW_PHP) > .php-version
	$(SF) composer update --no-interaction --no-progress --prefer-lowest --prefer-stable -W

full-test: check-deps check-code lint infection

check-deps:
	$(SF) composer outdated
	$(SF) composer validate
	$(SF) security:check

check-code: cs psalm stan

cs:
	$(SF) php vendor/bin/php-cs-fixer fix --verbose --allow-risky=yes
psalm:
	$(SF) php vendor/bin/psalm --no-progress --show-info=true --no-cache
stan:
	$(SF) php vendor/bin/phpstan --no-progress

lint:
	vendor/bin/neon-lint .
	vendor/bin/yaml-lint config tests --parse-tags

test:
ifdef FILTER
	$(SF) php vendor/bin/phpunit --filter $(FILTER)
else
	$(SF) php vendor/bin/phpunit
endif

cover:
	XDEBUG_MODE=coverage $(SF) php vendor/bin/simple-phpunit --coverage-xml=cov/xml --coverage-html=cov/html --log-junit=cov/junit.xml

infection:
	XDEBUG_MODE=coverage vendor/bin/infection --ansi --threads=$(nproc)
