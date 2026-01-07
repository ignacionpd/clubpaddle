<?php

# Vinculamos la ruta absoluta al directorio config.php desde db_conn.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/db_conn.php';

// CHECK DE EMAIL SOLO EN REGISTRO NUEVO DE USUARIO
function check_email($email, $mysqli_connection, &$exception_error_email)
{
    # Declaramos la sentencia $select_stmt como nula y luego trabajamos sobre ella
    # para prevenir errores y gestionar de forma más correcta la gestión de excepciones
    $select_stmt = null;

    # Se inicia la gestión del control de excepciones
    try {
        # Preparar la sentencia para buscar el email en la BBDD
        $select_stmt = $mysqli_connection->prepare('SELECT email FROM users_data WHERE email = ?');

        # Comprobamos si la sentencia se ha podido preparar correctamente
        if ($select_stmt === false) {
            error_log("No se pudo preparar la sentencia: " . $mysqli_connection->error);
            $exception_error_email = true;
            return false;
        }

        # Vinculamos el email a la sentencia
        $select_stmt->bind_param("s", $email);

        # Comprobar si se puede ejecutar la sentencia una vez preparada y se ejecuta
        if (!$select_stmt->execute()) {
            error_log("No se pudo ejecutar la sentencia: " . $select_stmt->error);
            $exception_error_email = true;
            return false;
        }

        # Guardamos el resulado de la sentencia tras su ejecución
        $select_stmt->store_result();

        return $select_stmt->num_rows > 0;
    } catch (Exception $e) {
        error_log("Error en la función check_user: " . $e->getMessage());
        $exception_error_email = true;
        return false;
    } finally {
        if ($select_stmt !== null) {
            $select_stmt->close();
        }
    }
}

// FUNCION PARA REGISTRO SOLAMENTE....NO ACTUALIZACION DE PERFIL......... CHEQUEA SI EXISTE NADA MAS 
function check_usuario($usuario, $mysqli_connection, &$exception_error_usuario)
{
    # Declaramos la sentencia $select_stmt como nula y luego trabajamos sobre ella
    # para prevenir errores y gestionar de forma más correcta la gestión de excepciones
    $select_stmt = null;

    # Se inicia la gestión del control de excepciones
    try {
        # Preparar la sentencia para buscar el email en la BBDD
        $select_stmt = $mysqli_connection->prepare('SELECT usuario FROM users_login WHERE usuario = ?');

        # Comprobamos si la sentencia se ha podido preparar correctamente
        if ($select_stmt === false) {
            error_log("No se pudo preparar la sentencia: " . $mysqli_connection->error);
            $exception_error_usuario = true;
            return false;
        }

        # Vinculamos el email a la sentencia
        $select_stmt->bind_param("s", $usuario);

        # Comprobar si se puede ejecutar la sentencia una vez preparada y se ejecuta
        if (!$select_stmt->execute()) {
            error_log("No se pudo ejecutar la sentencia: " . $select_stmt->error);
            $exception_error_usuario = true;
            return false;
        }

        # Guardamos el resulado de la sentencia tras su ejecución
        $select_stmt->store_result();

        # Se devuelve como resultado de la función un valor booleano
        # true si se ha encontrado que el usuario existe
        # false si no se ha encontrado el usuario en la BBDD
        return $select_stmt->num_rows > 0;
    } catch (Exception $e) {
        error_log("Error en la función check_usuario: " . $e->getMessage());
        $exception_error_usuario = true;
        return false;
    } finally {
        if ($select_stmt !== null) {
            $select_stmt->close();
        }
    }
}

function eliminar_registro($id_user, $mysqli_connection, &$exception_error_registro)
{
    # Declaramos la sentencia $delete_last_insert como nula y luego trabajamos sobre ella
    # para prevenir errores y gestionar de forma más correcta la gestión de excepciones
    $delete_last_insert = null;

    $delete_last_insert = $mysqli_connection->prepare("DELETE FROM users_data WHERE idUser = ?");
    # SI la sentencia NO se ha podido preparar
    if (!$delete_last_insert) {
        # Se guarda el error de preparación de la sentencia
        error_log("No se pudo preparar la sentencia " . $mysqli_connection->error);
        $exception_error_registro = true;
        return false;

        # SI la sentencia se ha podido preparar   
    } else {
        # Vinculamos los valores introducidos por el usuario a los valores de la sentencia de inserción
        $delete_last_insert->bind_param("i", $id_user);

        # SI la sentencia se ha podido ejecutar
        if ($delete_last_insert->execute()) {

            # Cerramos la sentencia
            $delete_last_insert->close();


            # SI NO se ha podido ejecutar la sentencia    
        } else {
            # Se guarda el error de ejecución en el error_log
            error_log("Error: " . $delete_last_insert->error);
            $exception_error_registro = true;
            return false;
        }
    }
}



function get_user_by_username($usuario, $mysqli_connection, &$exception_error)
{
    # Inicializar la senencia de selección como nula
    $select_stmt = null;
    # Inicializamos la variable de error asumiendo que inicialmente no hay ningún error
    #$exception_error = false;

    try {
        # Preparar la sentencia SQL necesaria para buscar al usuario a través su correo electrónico
        $query = "SELECT * FROM users_login WHERE usuario = ? LIMIT 1";
        $select_stmt = $mysqli_connection->prepare($query);
        # Una opción alternativa sería: 
        # $select_stmt = $mysqli_connection ->pepare('SELECT * FROM users_login WHERE usuario = ? LIMIT 1 ');

        if ($select_stmt === false) {
            error_log("No se pudo preparar la sentencia " . $mysqli_connection->error);
            $exception_error = true;
            return false;
        }

        # Vincular el correo electrónico a la sentencia
        $select_stmt->bind_param('s', $usuario);

        # Intentar ejecutar la sentencia de selección
        if (!$select_stmt->execute()) {
            error_log("No se puede ejecutar la sentencia " . $mysqli_connection->error);
            $exception_error = true;
            return false;
        }

        # Obtener el resultado de la consulta
        $result = $select_stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); # fetch_assoc() nos permite obtener los datos del resultado como un array asociativo (clave: valor)

            return $user;
        } else {
            // Si no se encuentra el usuario o no existe
            return false;
        }
    } catch (Exception $e) {
        error_log("Error al ejecutar la función get_user_by_username(): " . $e->getMessage());
        $exception_error = true;
        return false;
    } finally {
        // Nos aseguramos de cerrar la sentencia si existe
        if ($select_stmt !== null) {
            $select_stmt->close();
        }
    }
}


# ****************************USER_PROFILE************************

# Consultar los datos del usuario de la $_SESSION['user'] para cargarlos en el formulario de actualización de datos.
function consultarDatos()
{

    $mysqli = connectToDatabase();
    $idUser = $_SESSION['user_data']['id_user'];

    $sql = "
        SELECT nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo
        FROM users_data
        WHERE idUser = ?
    ";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idUser);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $user;
}


# CHECK DE EMAIL SOLO EN MODIFICACION DE USUARIO -> Función para chequear que si el usuario intenta cambiar de email, no exista ya en la base de datos, o si coincide con el suyo, no hacer nada
function check_email_modificar(string $email, int $idUser, mysqli $mysqli_connection): string {

    try {
        $sql = "
            SELECT idUser
            FROM users_data
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = null;
        $stmt = $mysqli_connection->prepare($sql);
        if (!$stmt) {
            throw new Exception(
                "Error prepare check_email_modificar(): " . $mysqli_connection->error
            );
        }

        $stmt->bind_param("s", $email);

        if (!$stmt->execute()) {
            throw new Exception(
                "Error execute check_email_modificar(): " . $stmt->error
            );
        }

        $stmt->store_result();

        // OPCION 1) No existe el email -> SE PUEDE INGRESAR
        if ($stmt->num_rows === 0) {
            $stmt->close();
            return 'EMAIL_NO_EXISTE';
        }

        //Existe el emai -> VER SI ES DE OTRO USUARIO O DEL MISMO

        $stmt->bind_result($idEncontrado);
        $stmt->fetch();
        $stmt->close();

        // OPCION 2) ES DEL MISMO USUARIO
        if ((int)$idEncontrado === $idUser) {
            return 'EMAIL_MISMO_USUARIO';
        }

        // OPCION 3) Existe pero es de otro usuario
        return 'EMAIL_OTRO_USUARIO';

    } catch (Exception $e) {
        error_log("Error al ejecutar la función check_email_modificar: " . $e->getMessage());
        throw $e; // propagamos la excepción
    }
    throw $e; // propagamos la excepción
   
}


//  Función de C_CITAS_CREATE.PHP
function validar_cita($fecha, $hora, $motivo)
{

    $errores = [];

    /* =============================
       FECHA
    ============================= */

    if (empty($fecha)) {
        $errores[] = "La fecha es obligatoria.";
    } else {
        $fecha_dt = DateTime::createFromFormat('Y-m-d', $fecha);
        $hoy = new DateTime('today');

        if (!$fecha_dt) {
            $errores[] = "La fecha no es válida.";
        } elseif ($fecha_dt < $hoy) {
            $errores[] = "No se puede reservar una cita en el pasado.";
        }
    }

    /* =============================
       HORA (08:00 → 23:00)
    ============================= */

    if (!preg_match('/^(0[8-9]|1\d|2[0-3]):00$/', $hora)) {
        $errores[] = "La hora debe estar entre las 08:00 y las 23:00.";
    }

    /* =============================
       MOTIVO
    ============================= */

    if (strlen(trim($motivo)) < 5) {
        $errores[] = "El motivo debe tener al menos 5 caracteres.";
    }

    return $errores;
}


// ------------ (ADMIN) USUARIOS_ADMINISTRACION------------------ //

/* ==========================
 OBTENER TODOS LOS USUARIOS
========================== */
function obtener_todos_los_usuarios(mysqli $mysqli): array
{

    $usuarios = [];
    $stmt = null;

    try {

        // Preparamos la consulta en una variable
        $sql = "
            SELECT 
                ud.idUser,
                ud.nombre,
                ud.apellidos,
                ud.email,
                ud.telefono,
                ud.fecha_nacimiento,
                ud.direccion,
                ud.sexo,
                ul.usuario,
                ul.rol
            FROM users_data ud
            INNER JOIN users_login ul ON ud.idUser = ul.idUser
            ORDER BY ud.nombre ASC
        ";

        // Se prepara la consulta a la BD con la consulta preparada $sql
        $stmt = $mysqli->prepare($sql);

        // Si la consulta no se pudo hacer, se arroja la excepción
        if (!$stmt) {
            throw new Exception("Error en prepare(): " . $mysqli->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error en execute(): " . $stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error al obtener resultados");
        }

        $usuarios = $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {

        error_log(
            "[ADMIN][obtener_todos_los_usuarios] " . $e->getMessage()
        );
       
        # Se redirige al usuario a la página de error505.html
        header('Location: ../views/error/error500.html');
        exit();

    } finally {

        if ($stmt !== null) {
            $stmt->close();
        }
    }

    return $usuarios;
}




/* ==========================
   CREAR NUEVO USUARIO
========================== */

function registrar_usuario_admin(mysqli $db, array $data): void
{
    try {
        $db->begin_transaction();

        /* ---------------- INSERT users_data ---------------- */
        $stmt = $db->prepare(
            "INSERT INTO users_data 
            (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception("Prepare users_data: " . $db->error);
        }

        $stmt->bind_param(
            "sssssss",
            $data['nombre'],
            $data['apellidos'],
            $data['email'],
            $data['telefono'],
            $data['fecha_nacimiento'],
            $data['direccion'],
            $data['sexo']
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute users_data: " . $stmt->error);
        }

        $idUser = $db->insert_id;
        $stmt->close();

        /* ---------------- INSERT users_login ---------------- */
        $stmt = $db->prepare(
            "INSERT INTO users_login 
            (idUser, usuario, contrasena, rol)
            VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception("Prepare users_login: " . $db->error);
        }

        $stmt->bind_param(
            "isss",
            $idUser,
            $data['usuario'],
            $data['password'],
            $data['rol']
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute users_login: " . $stmt->error);
        }

        $stmt->close();

        $db->commit();

    } catch (Exception $e) {
        $db->rollback();
        error_log("Error registrar_usuario_admin: " . $e->getMessage());
        throw $e; //
    }
}


/* ==========================
   MODIFICAR USUARIO REGISTRADO
========================== */
function modificar_usuario(
    $idUser,
    $nombre,
    $apellidos,
    $email,
    $telefono,
    $direccion,
    $sexo,
    $rol,
    $mysqli_connection
): bool {

    try {
        $stmt1 = $mysqli_connection->prepare("
            UPDATE users_data
            SET nombre=?, apellidos=?, email=?, telefono=?, direccion=?, sexo=?
            WHERE idUser=?
        ");

        $stmt1->bind_param(
            "ssssssi",
            $nombre,
            $apellidos,
            $email,
            $telefono,
            $direccion,
            $sexo,
            $idUser
        );
        $stmt1->execute();
        $stmt1->close();

        $stmt2 = $mysqli_connection->prepare("
            UPDATE users_login
            SET rol=?
            WHERE idUser=?
        ");

        $stmt2->bind_param("si", $rol, $idUser);
        $stmt2->execute();
        $stmt2->close();

        return true;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}


/* ==========================
   BORRAR USUARIO
========================== */
function borrar_usuario(int $idUser, mysqli $db): bool
{
    try {
        $db->begin_transaction();

        $stmt = $db->prepare(
            "DELETE FROM users_data WHERE idUser = ?"
        );
        if (!$stmt) {
            throw new Exception($db->error);
        }

        $stmt->bind_param("i", $idUser);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $stmt->close();

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollback();
        error_log("Error borrar_usuario: " . $e->getMessage());
        return false;
    }
}



//----------------------- ADMIN NOTICIAS  --------------------------//

/* ==========================
   OBTENER NOTICIAS
========================== */
function obtener_noticias(mysqli $mysqli_connection): array
{
    $sql = "
        SELECT n.*, u.nombre
        FROM noticias n
        JOIN users_data u ON n.idUser = u.idUser
        ORDER BY n.fecha DESC
    ";

    $result = $mysqli_connection->query($sql);

    if (!$result) {
        throw new Exception("Error al obtener noticias: " . $mysqli_connection->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

/* ==========================
   CREAR NOTICIA
========================== */
function crear_noticia(
    string $titulo,
    string $imagen,
    string $texto,
    string $fecha,
    int $idUser,
    mysqli $mysqli_connection
): void {

    $stmt = $mysqli_connection->prepare(
        "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser)
         VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception(
            "Error al preparar crear_noticia: " . $mysqli_connection->error
        );
    }

    $stmt->bind_param(
        "ssssi",
        $titulo,
        $imagen,
        $texto,
        $fecha,
        $idUser
    );

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        throw new Exception(
            "Error al ejecutar la sentencia de crear_noticia: " . $stmt-> $error
        );
    }

    $stmt->close();
}


/* ==========================
   BORRAR NOTICIA
========================== */
function borrar_noticia(
    int $idNoticia,
    mysqli $mysqli_connection
): void {

    // Primero obtenemos el nombre de la imagen
    $stmtSelect = $mysqli_connection->prepare(
        "SELECT imagen FROM noticias WHERE idNoticia = ?"
    );

    if (!$stmtSelect) {
        throw new Exception(
            "Error prepare SELECT imagen: " . $mysqli_connection->error
        );
    }

    $stmtSelect->bind_param("i", $idNoticia);

    if (!$stmtSelect->execute()) {
        $error = $stmtSelect->error;
        $stmtSelect->close();
        throw new Exception(
            "Error execute SELECT imagen: " . $error
        );
    }

    $result = $stmtSelect->get_result();
    $row = $result->fetch_assoc();
    $stmtSelect->close();

    if (!$row) {
        throw new Exception("La noticia no existe.");
    }

    $imagen = $row['imagen'];

    // Ahora borramos la noticia
    $stmtDelete = $mysqli_connection->prepare(
        "DELETE FROM noticias WHERE idNoticia = ?"
    );

    if (!$stmtDelete) {
        throw new Exception(
            "Error prepare DELETE noticia: " . $mysqli_connection->error
        );
    }

    $stmtDelete->bind_param("i", $idNoticia);

    if (!$stmtDelete->execute()) {
        $error = $stmtDelete->error;
        $stmtDelete->close();
        throw new Exception(
            "Error execute DELETE noticia: " . $error
        );
    }

    $stmtDelete->close();

    // Borramos la imagen física (si existe)
    $rutaImagen = __DIR__ . '/../uploads/noticias/' . $imagen;

    if (file_exists($rutaImagen)) {
        unlink($rutaImagen);
    }
}


/* ==========================
   ACTUALIZAR NOTICIA
========================== */

function actualizar_noticia(
    int $idNoticia,
    string $titulo,
    ?string $imagen,
    string $texto,
    string $fecha,
    mysqli $mysqli_connection
): void {

    if ($imagen) {
        $sql = "UPDATE noticias
                SET titulo=?, imagen=?, texto=?, fecha=?
                WHERE idNoticia=?";
    } else {
        $sql = "UPDATE noticias
                SET titulo=?, texto=?, fecha=?
                WHERE idNoticia=?";
    }

    $stmt = $mysqli_connection->prepare($sql);
    if (!$stmt) {
        throw new Exception($mysqli_connection->error);
    }

    if ($imagen) {
        $stmt->bind_param("ssssi", $titulo, $imagen, $texto, $fecha, $idNoticia);
    } else {
        $stmt->bind_param("sssi", $titulo, $texto, $fecha, $idNoticia);
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        throw new Exception(
            "Error al ejecutar sentencia en actualización de noticia: " . $error
        );
    }
    $stmt->close();
}




//***********************  CITAS  ********************/

/* ==========================
   OBTENER TODAS LAS CITAS
========================== */
function obtener_citas(mysqli $mysqli): array {

    $sql = "
        SELECT c.idCita, c.fecha_cita, c.hora_cita, c.motivo_cita,
               u.idUser, u.nombre, u.apellidos
        FROM citas c
        JOIN users_data u ON c.idUser = u.idUser
        ORDER BY c.fecha_cita, c.hora_cita
    ";

    $result = $mysqli->query($sql);

    if (!$result) {
        throw new Exception($mysqli->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}


/* ==========================
   CREAR CITA
========================== */
function crear_cita(
    int $idUser,
    string $fecha,
    string $hora,
    string $motivo,
    mysqli $mysqli
): void {

    $stmt = $mysqli->prepare(
        "INSERT INTO citas (idUser, fecha_cita, hora_cita, motivo_cita)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception($mysqli->error);
    }

    $stmt->bind_param("isss", $idUser, $fecha, $hora, $motivo);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();
}


/* ==========================
   ACTUALIZAR CITA
========================== */
function actualizar_cita(
    int $idCita,
    string $fecha,
    string $hora,
    string $motivo,
    mysqli $mysqli
): void {

    $stmt = $mysqli->prepare(
        "UPDATE citas
         SET fecha_cita = ?, hora_cita = ?, motivo_cita = ?
         WHERE idCita = ?"
    );

    if (!$stmt) {
        throw new Exception($mysqli->error);
    }

    $stmt->bind_param("sssi", $fecha, $hora, $motivo, $idCita);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();
}


/* ==========================
   BORRAR CITA
========================== */
function borrar_cita(int $idCita, mysqli $mysqli): void {

    $stmt = $mysqli->prepare(
        "DELETE FROM citas WHERE idCita = ?"
    );

    if (!$stmt) {
        throw new Exception($mysqli->error);
    }

    $stmt->bind_param("i", $idCita);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();
}
