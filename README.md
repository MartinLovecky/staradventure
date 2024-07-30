# php 8 is required also in php.ini enable extension=sodium

Before you can use this application you need to create .env file in root directory of your application and then add these variables:

### THESE will change !!! 

- DB_NAME = ""
- DB_USER = ""
- DB_HOST = "localhost"
- DB_PASS = ""
- CHAR = "utf8mb4"
- EMAIL_HOST = ""
- EMAIL_NAME = ""
- EMAIL_PASS = ""
- EMAIL_PORT = 587
- EKEY = $enc->generateKey();
- RECAPTCHA = ""
- CSRFKEY = ""

inside public directory is sql dir for all necessary DB tables

# Important
you need creare these folders in root:  compiles, public/img, .htaccess

and run in terminal
```bash
    composer install
    composer dump-autoload
```

