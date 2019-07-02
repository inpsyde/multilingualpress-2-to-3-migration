#!/bin/bash -x

docker-compose run -u 33 cli core install --url=$1 --title=WordPress --admin_user=admin --admin_password=password --admin_email=admin@example.com >/dev/null 2>&1
docker-compose run -u 33 cli core multisite-convert >/dev/null 2>&1
docker-compose run -u 33 cli site create --slug=es --title="Spanish" --email=admin@example.com >/dev/null 2>&1
docker-compose run -u 33 cli site create --slug=it --title="Italian" --email=admin@example.com >/dev/null 2>&1

mkdir -p ./test/acceptance/tests/_data
docker-compose run cli db export wp-content/plugins/multilingualpress-2-to-3-migration/test/acceptance/tests/_data/dump.sql >/dev/null 2>&1
