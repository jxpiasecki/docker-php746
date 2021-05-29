1. Initialize container network
```
    docker network create laravelnetwork
```

2. Build container
```
    docker stop $(docker ps -aq)
    docker-compose up -d --build
    docker-compose up
```
3. Test url (remember index must be in /public dir)
```
    http://laravel.localhost/index.php
```
4. Define db connection. Remember to use docker db container name.

```
    DB_CONNECTION=mysql
    #DB_HOST=127.0.0.1
    DB_HOST=laravel_mysql
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=root
```

```
    php artisan config:clear
    php artisan migrate:install
    php artisan migrate
```

5. Install breeze
```
    composer require laravel/breeze --dev
    
    php artisan breeze:install
    npm install
    npm run dev
    php artisan migrate
```
