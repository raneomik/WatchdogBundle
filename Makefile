
SF = symfony

up-deps:
	$(SF) composer update -W

check: cs psalm stan

cs:
	vendor/bin/php-cs-fixer fix
psalm:
	vendor/bin/psalm --no-progress --show-info=true
stan:
	vendor/bin/phpstan --no-progress

test:
ifdef FILTER
	$(SF) php vendor/bin/phpunit --filter $(FILTER)
else
	$(SF) php vendor/bin/phpunit
endif

cover: cov/junit.xml
	XDEBUG_MODE=coverage $(SF) php vendor/bin/phpunit --coverage-xml=cov/xml --coverage-html=cov/html --log-junit=cov/junit.xml

mutes:
	XDEBUG_MODE=coverage vendor/bin/infection --ansi --threads=$(nproc)
