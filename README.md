# api

## How to build
* `cp .env.example .env`
* `docker-compose up -d --build`

## Run
access http://127.0.0.1:8888

## Migration database
* `docker-compose run --rm api php artisan migrate --seed`

## Fix migration database error 
SQLSTATE[HY000] [2054] The server requested authentication method unknown to the client (SQL: select * from information_schema.tables where table_schema = fudosan and table_name = migrations and table_type = 'BASE TABLE')

Step 1: Login to mysql docker: account: "root" pasword: "root"
* `docker exec -it mysql mysql -u root -p`

Step 2: Run add privileges below:
* `mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';`
* `mysql> FLUSH PRIVILEGES;`

Step 3: Run migration
* `docker-compose run --rm api php artisan migrate --seed`

## Swagger config

The first time need run following code:
* `docker-compose run --rm api php artisan l5-swagger:generate`

access http://localhost:8888/api/documentation

## Run batch command
Ssh to development or production server.

Move api folder under root permisson
* `cd /var/www/api`

Custom update pay information:
* `php artisan ex:example 2020-08-27`