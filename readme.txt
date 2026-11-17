==============================================================
  TIENDA ONLINE EN PHP - Proyecto 1ª Evaluación (DWES, 2º DAW)
  Autor: Oleksandr Rudynskyy - IES Juan de Garay
==============================================================

REPOSITORIO EN GITHUB
---------------------
https://github.com/tokelad/tienda-php


1. DESCRIPCIÓN
--------------
Aplicación web en PHP puro (sin frameworks) para gestionar una tienda
online: catálogo de productos con sus categorías, CRUD completo de
productos, inicio de sesión de usuarios y control de acceso por roles.
Las páginas se generan en el servidor (PHP devuelve HTML) y el diseño
usa Bootstrap 5.


2. ESTRUCTURA DEL PROYECTO
--------------------------
tienda/
  public/          -> Páginas PHP. Es el DocumentRoot de Apache: lo
                      único accesible desde el navegador.
    index.php            Listado de productos y buscador
    header.php           Cabecera y menú (común a todas las páginas)
    footer.php           Pie de página
    login.php            Formulario de inicio de sesión
    logout.php           Cierre de sesión
    details.php          Detalles de un producto
    create.php           Crear producto
    update.php           Modificar producto
    delete.php           Eliminar producto
    update-image.php     Formulario para cambiar la imagen
    update_image_file.php  Procesa la subida de la imagen
    uploads/             Imágenes subidas de los productos
  src/             -> Clases de la aplicación (fuera de public/, el
                      navegador no puede acceder a ellas)
    config/Config.php    Configuración y conexión a la BD
    models/              Categoria, Producto, User
    services/            CategoriasService, ProductosService,
                         SessionService, UsersService
  database/init.sql      Crea las tablas e inserta datos de ejemplo
  docker-compose.yml     Contenedores: php-app, postgres-db y adminer
  Dockerfile             Imagen de PHP 8.2 + Apache + pdo_pgsql
  composer.json          Dependencias y autoload PSR-4
  .env.example           Variables de entorno (copiar como .env)

Organización del código:
  - Modelos: clases que representan los datos (una fila de una tabla).
  - Servicios: contienen todas las consultas SQL de cada tabla.
  - Páginas (public/): reciben la petición, usan los servicios y
    muestran el HTML.
  - Composer carga las clases automáticamente (autoload PSR-4) según
    su namespace (config, models, services).


3. LÓGICA DEL CRUD DE PRODUCTOS
-------------------------------
Todo el SQL está en ProductosService:
  - Leer: findAllWithCategoryName() devuelve los productos con el
    nombre de su categoría (JOIN con categorias) y permite buscar por
    marca o modelo. findById() devuelve un producto concreto.
  - Crear (create.php): formulario por POST, se validan los campos
    (obligatorios, precio y stock numéricos) y se muestran los errores
    junto a cada campo. save() genera un UUID y pone la imagen por
    defecto.
  - Modificar (update.php): el id llega por la URL, el formulario sale
    relleno con los datos actuales y update() guarda los cambios.
  - Eliminar (delete.php): pide confirmación, borra el archivo de
    imagen de uploads/ (si tiene una propia) y después el producto.
  - Imagen (update-image.php + update_image_file.php): se sube un
    JPG o PNG, se guarda en uploads/ con el UUID del producto como
    nombre y se actualiza la ruta en la base de datos.


4. GESTIÓN DE CATEGORÍAS Y PRODUCTOS
------------------------------------
Cada producto pertenece a una categoría (clave foránea categoria_id).
CategoriasService::findAll() carga las categorías desde la base de
datos para el desplegable de los formularios de crear y modificar.
En el listado se muestra el nombre de la categoría gracias al JOIN.


5. USUARIOS, ROLES Y SESIONES
-----------------------------
Usuarios de prueba:
  admin / admin1234   (roles ADMIN y USER)
  user  / user1234    (rol USER)

Permisos:
  Operación                Invitado   USER   ADMIN
  Ver listado y buscar     Sí         Sí     Sí
  Ver detalles             No         Sí     Sí
  Crear / modificar        No         No     Sí
  Cambiar imagen           No         No     Sí
  Eliminar                 No         No     Sí

Los roles están en la tabla user_roles y se cargan en el modelo User
como un array; el método isAdmin() comprueba si tiene el rol ADMIN.

SessionService (patrón Singleton) gestiona la sesión: inicia la
sesión, guarda si el usuario ha iniciado sesión, su nombre y si es
administrador, cuenta las visitas y la fecha del último login (se
muestran en index.php) y cierra la sesión tras 1 hora de inactividad.
Si no hay sesión, el usuario aparece como "Invitado".


6. BASE DE DATOS
----------------
PostgreSQL 12 en un contenedor Docker. Tablas: categorias, productos,
usuarios y user_roles (init.sql las crea con datos de ejemplo).
Acceso con PDO. La clase Config usa el patrón Singleton: lee los
datos de conexión del archivo .env (librería phpdotenv) y crea una
única conexión PDO para toda la petición, con los errores en modo
excepción.


7. SEGURIDAD
------------
Autenticación:
  - Las contraseñas se guardan cifradas con bcrypt y se comprueban con
    password_verify(). El login va por POST.
  - Al iniciar sesión se regenera el ID de sesión
    (session_regenerate_id) y la sesión caduca tras 1 hora sin uso.
Autorización:
  - Cada página comprueba el rol en el servidor (no basta con ocultar
    los botones): si no hay permiso, redirige a index.php o login.php.
Otros:
  - Consultas preparadas de PDO en todas las consultas con datos del
    usuario (protección contra inyección SQL).
  - htmlspecialchars() al mostrar datos (protección contra XSS).
  - Validación de los formularios en el servidor.
  - Imágenes: solo JPG/PNG, comprobando el tipo MIME real y la
    extensión; el nombre del archivo es el UUID del producto.
  - src/ está fuera de public/ y el archivo .env no se sube a GitHub.
Mejora pendiente: el borrado se hace con un enlace GET; sería más
seguro hacerlo con un formulario POST y un token CSRF.


8. REQUISITOS Y EJECUCIÓN
-------------------------
Requisitos: Docker Desktop (con Docker Compose) y Git.

  git clone https://github.com/tokelad/tienda-php.git
  cd tienda-php
  copy .env.example .env          (Linux/Mac: cp .env.example .env)
  docker compose up -d --build
  docker exec -it php_app composer install

Aplicación:  http://localhost:8080
Adminer:     http://localhost:8081
             (PostgreSQL, servidor postgres-db, usuario admin,
              contraseña adminPassword123, base de datos tienda)
