# Plasticina
Un sistema contable libre y gratuito para llevar la administración de tu negocio

indice

* [Configuración inicial](#configuración-inicial)
* [Licencia de código abierto](#licencia)

## Configuración inicial

1.1 - Instalación del motor de base de datos
Se recomienda usar ```mariadb 11.8.8-MariaDB``` para mantener la compatibilidad de todos los componentes

compruebe la versión
```mariadb --version```

1.2 - Crear la base de datos en su gestor de base de datos

```CREATE DATABASE plasticina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;```

1.3 - Configuración del .env y credenciales
Copie el .env.example en un nuevo archivo llamado .env y configure las credenciales (edite el ```change_me``` por un usuario y clave propios)

```
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plasticina
DB_USERNAME=change_me
DB_PASSWORD=change_me
```

1.4 - Ejecución de migraciones
Inicie una migración limpia para cargar las tablas en la base de datos  

```php artisan migrate:fresh```

1.5 - Genere la llave con artisan

```php artisan key:generate```

1.6 -  Ejecute el Seeder de la base de datos para poblar datos por defecto
```php artisan db:seed```

1.7 - Inicie el programa  
```php artisan serve```   
```npm run dev``` 

## Licencia

Plasticina es un proyecto de código abierto distribuido bajo la licencia **GNU AGPLv3**.

Eres libre de usar, modificar e incluso comercializar este sistema contable. Sin embargo, si modificas el código y lo ofreces como un servicio a través de una red (incluso si no distribuyes los archivos directamente), **estás obligado** a hacer que el código fuente de tus modificaciones esté disponible públicamente para tus usuarios bajo esta misma licencia.

Para más detalles, revisa el archivo `LICENSE` en este repositorio.