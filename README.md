Бэкенд партнерского кабинета


## Continuous Integration

```sh
docker run --rm \
        -v shared_composer:/.composer \
        -v $PWD:/project \
        -e DATABASE_SERVER_VERSION=mariadb-10.3.9 \
    docker.nalogka.com/builder:php myke build
```
