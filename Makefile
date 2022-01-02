install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
test:
	composer exec phpunit ./tests