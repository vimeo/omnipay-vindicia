all: style test

style:
	vendor/bin/phpcs --standard=PSR2 src && vendor/bin/phpcs --standard=PSR2 --error-severity=1 --warning-severity=6 tests

test:
	vendor/bin/phpunit

psalm:
	vendor/bin/psalm
