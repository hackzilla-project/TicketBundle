cs:
	vendor/bin/php-cs-fixer fix --verbose

cs_dry_run:
	vendor/bin/php-cs-fixer fix --verbose --dry-run

test:
	vendor/bin/simple-phpunit -c phpunit.xml.dist --exclude-group vichuploaderbundle

test_with_vichuploaderbundle:
	vendor/bin/simple-phpunit -c phpunit.xml.dist
