Каталог лекарственных препаратов
================================
Тестовое задание

Требования
----------
1. [composer](https://getcomposer.org)
2. [docker](https://www.docker.com/)
3. [git](https://git-scm.com/) (опционально)

Установка
---------
Скачайте исходный код
+ если у вас установлен git, выполните команду
    ```
    git clone ...
    ```
+ либо скачайте и разверните zip-архив

Перейдите в папку drugs
```
$ cd drugs
```

Запустите Docker-контейнер
```
$ docker-compose up -d
```

Запустите веб-сервер в Docker-контейнере 
```
$ docker exec php-fpm symfony serve -d
```

Примените миграции для создания БД и таблиц 
```
$ docker exec php-fpm bin/console doctrine:migrations:migrate -n
```

Примените фикстуры для генерации тестовых синтетических данных
```
$ docker exec php-fpm php bin/console doctrine:fixtures:load -n
```

Использование
-------------
+ Вариант 1

    Для получения JWT токена запустите команду:
    ```
    $ curl -X POST -H "Content-Type: application/json" http://127.0.0.1:5000/api/login_check -d '{"username":"user","password":"password"}'
        -> { "token": "[TOKEN]" } 
    ```
    После этого нужно вставлять полученный токен в запросы к API:
    ```
    $ curl -H "Authorization: Bearer [TOKEN]" http://localhost:8000/api/drugs
    ```
+ Вариант 2
    
    Откройте в браузере адрес [http://127.0.0.1:5000](http://127.0.0.1:5000) и авторизуйтесь, используя имя пользователя `username` и пароль `password` 