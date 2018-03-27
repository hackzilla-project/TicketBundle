cs:
	php-cs-fixer fix --verbose

cs_dry_run:
	php-cs-fixer fix --verbose --dry-run

test:
	vendor/bin/phpunit -c phpunit.xml.dist
