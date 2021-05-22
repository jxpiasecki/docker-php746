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