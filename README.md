Before you can use this application you need to create .env file in root directory of your application and then add these variables:
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
- ENON = $enc->generateNonce();
- RECAPTCHA = ""

# Important
you need also creare these files / folders
cache/
public/img/
.htaccess
.gitignore

and run in terminal
```bash
    composer install
    composer dump-autoload
```

