## Getting started
#### Entorno

``` bash
$ cp .env.dist .env

# Debemos indicar las variables de entorno
    root_pwd=my-super-secret
    db_name=api_platform
    db_user=root
    db_password=
```
    
#### Docker: Levantamos los servicios y comandos de interes

``` bash
# levantar todos los servicios
$ docker-composer up -d
$ docker-composer up -d --build    # para forzar la creación de las imágenes

# ver todos los contenedores levantados en la carpeta actual
$ docker-composer ps
```

Preparar el contenedor: 
--
Añadimos un administrador a la base de datos vía comando:
``` bash
# Abrimos la consola dentro del contenedor "app"
$           docker exec -it workshop-symfony4_app_1 sh
/srv/app$   php bin/console licensedrawer:create-admin-user
```

#### Generate the SSH keys:

https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#getting-started
``` bash
# Para generar las key debes usar la pass phrase declarada en el entorno (.env): JWT_PASSPHRASE
$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

#### Generar token

``` bash
$ curl -X POST -H "Content-Type: application/json" http://localhost:8100/api/login_check -d '{"username":"admin","password":"admin"}'
```
``` json
Respuesta:
{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ......"}
```

#### Test API Platform
URL: http://localhost/api/docs.html
>Importante indicar el Bearer en el formulario "Authorize": <br>
>Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ......





