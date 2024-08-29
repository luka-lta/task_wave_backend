docker_run := 'docker compose -f docker-compose.development.yml run --rm php-fpm'

lint:
    {{docker_run}} vendor/bin/phpmd src text codesize
    {{docker_run}} vendor/bin/phpcs src

dev:
    docker compose -f docker-compose.development.yml up
