phpstan:
	vendor/bin/phpstan analyse  -l 7 src

phpcsfixer:
	php-cs-fixer fix src
