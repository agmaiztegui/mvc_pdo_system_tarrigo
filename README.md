## Miembros Grupo:

Garcia Maiztegui Agustin
Rojas	Curulla Martin César
Rosso	Luis
Rojas	Luana
Tomas	Schulteis 
Tomas Arrigo

# Sistema de Autenticación Seguro

# Mockup

https://xd.adobe.com/view/788666e5-3f93-42b3-91e1-e9249a475fc0-2dd5/grid

## Descripción
Este proyecto es una simulación de un sistema de autenticación de usuarios (login) con roles de usuario y administrador, incluyendo funciones de registro, login, y recuperación de contraseñas. Se ha desarrollado bajo las mejores prácticas de seguridad DevSecOps.

## Requisitos
- PHP 7.4+
- MySQL 5.7+
- Servidor Apache/Nginx

## Instalación
1. Clonar el repositorio.
2. Crear la base de datos usando las sentencias incluídas en el archivo 'db.sql' (ubicado en la carpeta raíz del proyecto).
3. Configurar en /model/model.php la conexion a la DB
5. Asegurar el file model.php no sea accesible, solo de manera local.
5. Asegúrese de que las configuraciones de seguridad del servidor web estén implementadas según las recomendaciones.

## Consideraciones de Seguridad
- Validación de contraseñas seguras.
- Uso de consultas preparadas para prevenir inyección SQL.
- Tokens CSRF en formularios para prevenir ataques CSRF.
- Protección contra XSS mediante el uso de `htmlspecialchars`.
- Configuración de headers de seguridad.
- Gestión de sesiones segura.
  
## Ejecutar la Aplicación
1. Asegúrese de que su servidor esté ejecutándose.
2. Navegue a `index.php` en su navegador.
3. Regístrese, inicie sesión y utilice las funciones del sistema.
 
