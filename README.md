# Sistema de solicitudes de soporte técnico

**Estudiante:** Pincay Mora Ana Belén
**Actividad:** Actividad Integradora 3

## Descripción

Aplicación web desarrollada en PHP y MySQL/MariaDB para registrar, consultar y resolver
solicitudes de soporte técnico. El acceso a datos se hace con PDO y sentencias preparadas, y
las salidas se sanitizan para prevenir inyección SQL y XSS.

## Funcionalidades

- **Registro** de solicitudes con validación en el navegador y en el servidor.
- **Consulta** en una tabla responsiva, con filtros por prioridad, estado y rango de fechas.
- **Resolución:** una solicitud pendiente se puede marcar como finalizada o rechazada, con un
  comentario obligatorio.
- **Menú lateral** vertical y desplegable, adaptado a celular.

## Tecnologías utilizadas

- PHP 8 con PDO
- MySQL / MariaDB (XAMPP)
- HTML5, CSS3 y JavaScript
- Git y GitHub

## Seguridad

- Sentencias preparadas con parámetros con nombre (INSERT, UPDATE y SELECT con filtros).
- Sanitización de salidas con `htmlspecialchars()`.
- Validación en cliente y servidor, con listas de valores permitidos.
- Conexión en un archivo de configuración separado y errores controlados con `try/catch`
  (mensaje genérico para el usuario y detalle solo en el registro del servidor).

## Estructura del proyecto

```
AI3_DisenoWeb_Pincay/
├── index.php 
├── registro.php 
├── consulta.php 
├── resolver.php 
├── README.md 
├── config/
│   └── conexion.php 
├── includes/ 
│   ├── head.php 
│   ├── header.php 
│   ├── menu.php 
│   ├── footer.php 
│   └── funciones.php 
├── assets/
│   ├── css/
│   │   └── styles.css
│   └── js/
│       ├── menu.js
│       └── formulario.js
└── database/
    ├── db_sistema_web.sql
    └── actualizar_resolucion.sql
```

## Cómo desplegar el proyecto localmente

1. Instalar [XAMPP](https://www.apachefriends.org/) e iniciar los módulos **Apache** y **MySQL**.
2. Clonar el repositorio dentro de la carpeta `htdocs`:
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/snsbpm98-dev/AI3_DisenoWeb_PincayAna.git
   ```
3. Abrir `http://localhost/phpmyadmin`, entrar a la pestaña **Importar** y seleccionar
   `database/db_sistema_web.sql`. El script crea la base `db_sistema_web` y la tabla
   `solicitudes`.
4. Revisar las credenciales en `config/conexion.php`. Por defecto son las de XAMPP
   (usuario `root`, sin contraseña).
5. Abrir en el navegador: `http://localhost/AI3_DisenoWeb_PincayAna/`

> El archivo `database/actualizar_resolucion.sql` solo es necesario si la base se creó con una
> versión anterior del script, sin los campos de resolución.