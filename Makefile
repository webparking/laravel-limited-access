# Constants
EXEC = "docker-compose exec -u$(user) workspace"

# Default variables
user := "user"

# Docker environment
start: up composer-install

stop:
	docker-compose stop

up:
	docker-compose up -d

# Composer
composer:
	"$(EXEC)" composer $(cmd)

composer-install:
	"$(EXEC)" composer install

# Code quality tools
cs-fix:
	"$(EXEC)" composer php-cs-fixer

phpstan:
	"$(EXEC)" composer phpstan

phpunit:
	"$(EXEC)" composer phpunit

test-all: cs-fix phpstan phpunit

# Workspace
cmd:
	clear && "$(EXEC)" bash || true && clear
