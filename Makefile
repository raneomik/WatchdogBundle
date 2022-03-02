
SF = symfony

up-deps:
	 $(SF) composer require --no-progress --no-update --no-scripts --dev \
              symplify/coding-standard:* symplify/phpstan-rules:* \
              phpstan/phpstan-symfony:* ekino/phpstan-banned-code:* phpstan/phpstan-phpunit:* phpstan/extension-installer:* phpstan/phpstan:* \
              psalm/plugin-symfony:* vimeo/psalm:* \
              infection/infection:*
	$(SF) composer update --no-interaction --no-progress -W

down-deps:
	 $(SF) composer remove --no-progress --no-update --no-scripts --dev \
              symplify/coding-standard symplify/phpstan-rules \
              phpstan/phpstan-symfony ekino/phpstan-banned-code phpstan/phpstan-phpunit phpstan/extension-installer phpstan/phpstan \
              psalm/plugin-symfony vimeo/psalm \
              infection/infection
	$(SF) composer update --no-interaction --no-progress --prefer-lowest -W

check: cs psalm stan

cs:
	vendor/bin/php-cs-fixer fix
psalm:
	vendor/bin/psalm --no-progress --show-info=true
stan:
	vendor/bin/phpstan --no-progress

lint:
	vendor/bin/neon-lint
	vendor/bin/yaml-lint

test:
ifdef FILTER
	$(SF) php vendor/bin/phpunit --filter $(FILTER)
else
	$(SF) php vendor/bin/phpunit
endif

cover:
	XDEBUG_MODE=coverage $(SF) php vendor/bin/simple-phpunit --coverage-xml=cov/xml --coverage-html=cov/html --log-junit=cov/junit.xml

mutes:
	XDEBUG_MODE=coverage vendor/bin/infection --ansi --threads=$(nproc)
