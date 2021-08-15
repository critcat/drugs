Каталог лекарственных препаратов
================================
Тестовое задание

Требования
----------
1. [docker](https://www.docker.com/)
2. [git](https://git-scm.com/)

Установка
---------
Скачайте исходный код
```
git clone https://github.com/critcat/drugs.git
```
Перейдите в папку drugs
```
cd drugs
```
Запустите Docker-контейнер
```
docker-compose up -d
```
Установите зависимости
```
docker exec php-fpm composer i
```
Запустите веб-сервер в Docker-контейнере 
```
docker exec php-fpm symfony serve -d
```
Примените миграции для создания БД и таблиц 
```
docker exec php-fpm php bin/console doctrine:migrations:migrate -n
```
Примените фикстуры для генерации тестовых синтетических данных
```
docker exec php-fpm php bin/console doctrine:fixtures:load -n
```
Сгенерируйте пары SSL ключей
```
docker exec php-fpm php bin/console lexik:jwt:generate-keypair
```

Использование
-------------
Для получения JWT токена запустите команду:
```
curl -X POST -H "Content-Type: application/json" http://127.0.0.1:5000/api/login_check -d '{"username":"user","password":"password"}'
    -> { "token": "[TOKEN]" } 
```
После этого нужно вставлять полученный токен в запросы к API:
```
curl -H "Authorization: Bearer [TOKEN]" http://127.0.0.1:5000/api/drugs
```

Тесты
-----
```
php bin/phpunit
```

Генерация документации в формате OpenAPI
-
+ при наличии Composer'a
    ```
    composer openapi-create
    ```
+ при отсутствии Composer'a
    ```
    ./vendor/bin/openapi --output ./public/openapi.yaml ./src
    ```
Файл `openapi.yaml` будет сгенерирован в папке `public`. Его можно открыть в Swagger UI. Для этого откройте в браузере адрес [http://127.0.0.1:5000/dist/](http://127.0.0.1:5000/dist/) и вверху в поле ввода вставьте адрес `http://127.0.0.1:5000/openapi.yaml`