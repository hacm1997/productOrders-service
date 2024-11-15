# ProductOrderService

ProductOrderService es una API de backend construida en Laravel que permite gestionar productos y órdenes, incluyendo funcionalidad de autenticación, exportación de datos a Excel, y protección de rutas.

Nota: Las peticiones puedes hacerla desde Swagger o descargar la colección de la carpeta Postman del repositorio

## Requisitos previos

-   PHP >= 8.1
-   Composer
-   MySQL o cualquier otra base de datos compatible con Laravel
-   Node.js y NPM (para gestionar dependencias front-end si es necesario)
-   Laravel >= 10.x
-   Laravel Sanctum para autenticación de usuarios

## Configuración Ejecutando un contenedor en Docker

## Instrucciones para Ejecutar en Docker

### 1. Construye y levanta los contenedores:

```bash
docker-compose up -d
```

### 2. Migra la base de datos:

```bash
docker-compose exec app php artisan migrate
```

La aplicación estará disponible en http://127.0.0.1:8081

Phpmyadmin estará disponible en http://127.0.0.1:8083

#### La documentación de la api en Swagger estrá disponible en http://127.0.0.1:8081/api/documentation#/

## Configuración Ejecución manual

### Paso 1: Clonar el repositorio

Clona el repositorio en tu máquina local.

```bash
git clone <URL_DEL_REPOSITORIO>
cd ProductOrderService
```

### Paso 2: Instalar dependencias

Instala todas las dependencias del proyecto usando Composer.

```bash
composer install
```

### Paso 3: Ejecutar migraciones

Ejecuta las migraciones para crear las tablas en tu base de datos.

```bash
composer install
```

### Paso 4: Configurar Laravel Sanctum para Autenticación (opcional)

Publica la configuración de Sanctum:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Ejecuta las migraciones de Sanctum para crear las tablas necesarias para tokens de autenticación:

```bash
php artisan migrate
```

### Paso 5: Iniciar el Servidor de Desarrollo

Para iniciar el servidor de desarrollo de Laravel, utiliza el siguiente comando:

```bash
php artisan serve
```

La aplicación estará disponible en http://127.0.0.1:8000. o en el purto 8001 si el 8000 está en uso

## Uso de la API

### Autenticación

### Registro de Usuario

Endpoint: POST /api/register

Campos:

name: Nombre del usuario

email: Email del usuario

password: Contraseña

password_confirmation: Confirmación de la contraseña

Inicio de Sesión

Endpoint: POST /api/login

Campos:

email: Email del usuario

password: Contraseña

Este endpoint devuelve un token que debe ser utilizado en todas las solicitudes protegidas.

#### Cerrar Sesión

Endpoint: POST /api/logout

Autenticación: Requiere el token generado en el inicio de sesión.

## Endpoints Principales

### Productos

Listar productos: GET /api/products

Ver producto por ID: GET /api/products/{id}

Crear producto: POST /api/products

Actualizar producto: PUT /api/products/{id}

Eliminar producto: DELETE /api/products/{id}

Exportar productos a Excel: GET /api/products/export

### Órdenes

Listar órdenes: GET /api/orders

Ver orden por ID: GET /api/orders/{id}

Crear orden: POST /api/orders

Actualizar orden: PUT /api/orders/{id}

Eliminar orden: DELETE /api/orders/{id}

Exportar órdenes a Excel: GET /api/orders/export

## Exportación de Datos

Para exportar los datos a un archivo Excel, se pueden utilizar los siguientes endpoints:

Exportar productos: GET /api/products/export

Exportar órdenes: GET /api/orders/export

Los archivos se generarán en formato .xlsx y serán descargables directamente desde el endpoint.

#### La documentación de la api en Swagger estrá disponible en http://127.0.0.1:8000/api/documentation#/ o http://127.0.0.1:8001/api/documentation#/
