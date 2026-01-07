|- README.md: La web se trata sobre un predio ficticio de pistas de Paddle donde se podrán hacer reservas (citas) para alquilar pistas. También tienen noticias que suben los mismos usuarios (admin) y noticias sobre el mundo del paddle hoy, junto con enlaces de páginas de interés sobre este deporte (tiendas, noticias, etc).
Para acceder a la web, hay que entrar primero desde la ruta localhost htdocs/MySQL/Trabajo obligatiorio PHP.


|- index.php: Es la página principal donde tiene 3 vistas que se podrán accesder como visitante: noticias, registro y login. Cuenta en su cuerpo principal con una presentación del sitio, donde se detallan las actividades y asuntos de interés del lugar. También cuenta con una presentación de imágenes de las instalaciones del predio, y al final, la lista de los enlaces mencionados anteriormente.

|-VIEWS: Carpeta donde contendrán las vistas de las páginas que acceden los visitantes, usuarios y administradores.
|--noticias.php: Contiene diversas noticias sobre el mundo del Paddle hoy, publicadas por sus administradores.
|--registro.php: Vista que contiene el formulario de registro de usuarios nuevos.
|--login.php: Vista en la cual el usuario ya registrado puede loguearse y acceder a la página para poder interactuar con otras vistas según el tipo de usuario que sea.

|-VIEWS/admin: Carpeta que continene las vistas de acceso exclusivo para los usuarios administradores.
|--admin_profile.php: Página que contiene un formulario con los datos del perfil del usuario administrador entrante, donde puede modificarlos, salvo el nombre de usuario. También tiene otro pequeño formulario aparte para cambiar la contraseña.
|--citas_administracion.php: Mediante ella puede crear nuevas citas, asociándolas a un usuario específico, junto con la fecha, hora y motivo de la misma para hacer aclaraciones si es necesario. Se pueden elegir de 8am a 00am (siendo las 23.00hs la última cita posible) y siempre cada 1 hora en punto se puede realizar la cita. Al suponer que hay 8 pistas disponibles, esa es la cantidad máxima que puede haber de reservas para esa hora específica. También puede desde la página, borrar y modificar citas existentes.
|--noticias_administracion.php: Desde allí se pueden cargar nuevas noticias que se verán en la página noticias desde la vista pública del sitio. También contiene todas las noticias que hay cargadas con la opción de modificarlas y borrarlas una por una.
|--usuarios_administrador.php: Desde aquí se pueden administrar los usuarios existentes cambiandoles sus datos, e incluso el rol de usuario, salvo la contraseña y el nombre de usuario. También puede, desde un formulario aparte, crear nuevos usuarios y ahí sí puede asignarles una clave.

|-VIEWS/ERRORS/error500.html: Carpeta y archivo único que se utilizará para redirigir hacia ahí a un usuario ante cualquier error grave de la página con un mensaje inclusive.

|-VIEWS/user: Carpeta donde contendrán las vistas del usuario (no admin).
|--citas.php: Archivo vista de usuario donde puede solicitar nuevas citas y ver las que tiene pendientes, pudiendo modificarlas y borrarlas.
|--user_profile.php: Contiene la vista con el perfil del usuario donde puede moficiar sus datos y contraseña, pero no el nombre de usuario.

|-UPLOADS: Carpeta que contendrán otras carpetas o archivos subidos por los usuarios.

|-UPLOADS/noticias: Carpeta con las fotos de las noticias subidas por los usuarios.

|-DATABASE: Carpeta que contendrá el o los archivos de las bases de datos con toda la información para la creación de las mismas, sus tablas, atributos, etc.
|--club_paddle.sql:  Archivo sql de la base de datos usada por la web.

|-CONTROLLERS: Carpeta que contendrá los controladores de la página web (comunicación con formularios, bases de datos, funciones, validaciones, etc)
|--CONTROLLERS/admin: Carpeta que contiene los controladores exclusivos para las vistas de los administradores.
|--c_admin_password.php: Controlador para comunicarse con el formulario de actualización de la clave del usuario admin logueado.
|--c_admin_profile.php: Controlador que validará los datos ingresados en admin_profile.php, mediante el formulario de la vista del perfil del administrador. 
|--c_citas_admin.php: Controla los datos enviados desde la página citas_administración.php.
|--c_noticias_admin.php: Corrobora los datos ingresados en la vista noticias_administracion.php.
|--c_usuarios_admin.php: Controla los datos enviados desde los formularios de usuarios_administración.php.

|-CONTROLLERS/users: Carpeta contenedora de todos los controladores de las vistas de user (usuario no admin).
|--c_citas_create.php: Controla lo enviado por la vista citas.php del usuario puntualmente en el formulario de creación de cita.
|--c_citas_data.php: Mediante este controlador enviamos la información de las citas actuales del usuario a un formulario a la página citas.php.
|--c_citas_delete.php: Recibe los datos de la cita a borrar mediante un formulario desde citas.php.
|--c_citas_update.php: Se encarga de la modificación de las citas existentes del usuario en citas.php.
|--c_user_profile.php: Envía los datos del usuario a la página user_profile.php y controla los que quiere cambiar mediante el mismo formulario, como también el de cambio de contraseña.

|-CONTROLLERS/VALIDATIONS/v_inputData.php: Carpeta que contiene (en este caso uno solo -> v_inputData.php) los archivos con las validaciones de los datos ingresados a los formularios en cada formulario de ingreso de datos, devolviendo a los controladores los resultados de las comprobaciones necesarias (regex, por ejemplo).

|--.env.php: Contiene variables donde se guarda la información relevante para la conexión con la base de datos.
|--.htaccess: Archivo de Apache donde configuramos el código para proteger el archivo .env.php (contenido importante)
|--c_login.php: Controlador de login.php donde recibe los datos ingresados para el inicio de sesión.
|--c_noticias.php: Carga las noticias que hay en la BD, publicándolas en la vista noticias.php.
|--db_conn.php: Controlador encargado de la comunicación con la base de datos.
|--db_functions: Controlador que tiene casi todas las funciones con las que trabajará la página web, aislandolas de los controladores que se encargan de verificar los formularios y comunicarse con la web.
|--logout.php: Controlador encargado del cierre de sesión de los usuarios.

|-CONFIG: Carpeta contenedora de los archivos principales referidos a la configuración y protección de cierta información.
|--config.php: Archivo donde se asegura que las páginas (que lo llamen previamente) obtengan los errores de manera más segura si los hubiesen.
|--.htaccess: Archivo que protege a config.php.

|-ASSETS: Carpeta que contendrá distintas carpetas y archivos como imágenes, archivos de estilos, de scripts, etc.
|--ASSETS/css/estilos.css: Carpeta y archivo que contiene todos los estilos de la web.
|--ASSETS/images: Carpeta que contiene los archivos de imágnes de la web como los de la portada, favicon, encabezado y la carpeta de las imagenes de los enlaces de las webs relacionadas.
|--ASSETS/scripts: Carpeta que contiene los archivos scripts de distintas vistas.
|--show_password.js: Controla el checkbox para visualizar la contraseña al registrarse en registro.php.
|--v_citas_admin.js: Contiene las animaciones y validaciones de los formularios de citas_administracion.php.
|--v_citas.js: Realiza lo mismo pero en la vista de citas.php del usuario no admin.
|--v_login.js: Verifica el login del usuario en login.php.
|--v_noticias_admin.js: Controla también los formularios y animaciones en la vista noticias_administracion.php.
|--v.register.js: Controla el formulario de registro y los datos ingresados antes del envío del formulario en registro.php.
|--v_update_user.js: Verifica los datos ingresados por el usuario para la actualización de su perfil.
|--v_usuarios_admin.js: Realiza las mismas funciones de animación y control en la vista usuarios_administracion.php.


// ACLARACION: Tuve que agregar la columna "horas_cita" a la tabla "citas" de la BD ya que contemplé que al hacer la web sobre un sitio de pistas, se debe especificar una hora específica. 