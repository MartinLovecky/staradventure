# [php 8.3.0](https://www.php.net/releases/8.3/en.php) is required 
### In php.ini enable extension
1. extension=pdo_mysql
2. extension=sodium

### In public directory: 
1. .env file - move it to root dir
2. sql for all necessary DB tables

#### you need creare these folders:  
compiles, public/img

and run in terminal
```bash
    composer install
    composer dump-autoload
```

