all: no_psalm

no_psalm: style test

with_psalm: style psalm test

style:
	vendor/bin/phpcs --standard=PSR2 src && vendor/bin/phpcs --standard=PSR2 --error-severity=1 --warning-severity=6 tests

test:
	vendor/bin/phpunit

psalm:
	vendor/bin/psalm
