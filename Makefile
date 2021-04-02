ps:
	docker-compose ps

up:
	docker-compose up -d --remove-orphans

upb:
	docker-compose up -d --build --remove-orphans

down:
	docker-compose down

stop:
	docker-compose stop

#make rs name=php
rs:
	docker-compose restart $(name)
nrs:
	docker-compose restart nginx

#make exec name=php
exec:
	docker-compose exec $(name) /bin/bash || true
ex: exec

php:
	docker-compose exec php /bin/bash || true

