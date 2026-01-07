<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../db_functions.php';
require_once __DIR__ . '/../validations/v_inputData.php';

# Comprobamos si existe una sesión activa y en caso de que no sea así la creamos.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

# Comprobamos si existe una variable de SESSION y que sea ADMIN, y sino, redirigimos a index.php
if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

//************************* VALIDACION FORMULARIO "REGISTRAR NUEVO USUARIO"*************************** */

# Comprobamos si la información llega a través del método POST y del formulario con submit "registrarse"
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro_admin'])){
        /* ---------- SANITIZACIÓN ---------- */
    # En primer lugar obtenemos los datos del formulario saneados
    $nombre = htmlspecialchars($_POST['user_name']);
    $apellidos = htmlspecialchars($_POST['user_lastname']);
    $email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'user_tel', FILTER_SANITIZE_NUMBER_INT);
    
    # Validamos "fecha de nacimiento" (tipo input date)
    $validacion_fecha = validar_fecha_nacimiento($_POST['user_date'] ?? null);  
    /* Si ESTA OK la fecha, nos devuelve un array con la CLAVE "ok" en TRUE, y sino en FALSE.    
        -> Si está en TRUE, recogemos la fecha convertida en string (compatible con BD) con la clave $validacion_fecha[fecha]
        -> Si está con FALSE, recogemos el ERROR con $validacion_fecha[error];
    */

    $direccion = htmlspecialchars($_POST['user_adress']);
    $sexo =  $_POST['user_sex'];
    $usuario = htmlspecialchars($_POST['user_login_name']);
    $pass = $_POST['user_password'];
    $rol = $_POST['user_rol'];

    /* ---------- VALIDACIONES ---------- */
     $errores_validacion = validar_registro($nombre, $apellidos, $email, $telefono,  $usuario, $pass);
     
    # Comprobar si se han generado errores de validacion o no
    if(!empty($errores_validacion) || !$validacion_fecha['ok']){
        # Si hay errores de validación vamos a guardarlos en una cadena para mostrarselos al usuario
        $mensaje_error = "";

        # Recorremos el array de errores_validación para concatenar los mensajes en la variable $mensaje_error
        foreach($errores_validacion as $clave => $mensaje){
            $mensaje_error .= $mensaje . "<br>";
        }

        # Si el array fecha_nacimiento tiene la clave "ok" como "false", concatenamos también a la variable $mensaje_error, el valor de su clave "error" que tiene que tener dentro
        if(!$validacion_fecha['ok']){
            $mensaje_error .= $validacion_fecha['error'];
        }

        # Asignamos la cadena de errores a $_SESSION['mensaje_error']
        $_SESSION['mensaje_error'] = $mensaje_error;

        header("Location: ../.../views/admin/usuarios_administracion.php");
        exit();
    }

     # Intentamos realizar un registro sencillo en "users_data" con los campos a registrar sólo en ella
    try{
        # Declaramos la variable que registrará si se ha producido una excepción durante el proceso que
        # comprueba si el usuario que se está intentando registrar YA existe en la base de datos.
        $exception_error_email = false;
        $exception_error_usuario = false;
        $errores = [];

        # SI el resultado de check_email es TRUE (ya existe el email)
        if(check_email($email, $mysqli_connection, $exception_error_email)){
            # Esablecemos un mensaje de error
            $errores['email'] = "Ya existe un usuario registrado con ese email.";
        }
        
        # SI el resultado de check_usuario es TRUE (ya existe el usuario)
        if(check_usuario($usuario, $mysqli_connection, $exception_error_usuario)){
            # Esablecemos un mensaje de error
            $errores['usuario'] = "Ya existe un usuario registrado con ese nombre de usuario. <br>";
        }
        
        if(!empty($errores)){
            
            # Recorremos el array $errores e ingresamos su contenido al mensaje de sesión que mostraremos en pantalla
            foreach($errores as $clave => $error){
                $_SESSION['mensaje_error'] .= $error . '<br>';
            }
                        
            # Redirigimos al usuario a la página de registro
            header("Location: ../../views/admin/usuarios_administracion.php");
            exit(); 

        # SI el resultado de check_email() y check_usuario() es FALSE    
        }else{
            # SI se produjo una excepción durante el proceso de comprobación
            if($exception_error_email == true || $exception_error_usuario == true){
                # Se redirige al usuario a la página de error 500
                header('Location: ../../views/errors/error500.html');
                exit(); 
            # SI el usuario NO existe y NO hay excepciones
            }else{
                
                // Ingresamos el valor de la fecha convertida en string del array $validacion_fecha['fecha'] a la variable $fecha_nacimiento para que quede pueda leerse como string 
                $fecha_nacimiento = $validacion_fecha['fecha'];
            }
        }

        $pass_hash = password_hash($pass, PASSWORD_BCRYPT);

        registrar_usuario_admin($mysqli_connection, [
            'nombre'           => $nombre,
            'apellidos'        => $apellidos,
            'email'            => $email,
            'telefono'         => $telefono,
            'fecha_nacimiento' => $validacion_fecha['fecha'],
            'direccion'        => $direccion,
            'sexo'             => $sexo,
            'usuario'          => $usuario,
            'password'         => $pass_hash,
            'rol'              => $rol
        ]);

        $_SESSION['mensaje_exito'] = "ÉXITO: El usuario se ha registrado correctamente";
        header("Location: ../../views/admin/usuarios_administracion.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = "Error interno al registrar el usuario.";
        header("Location: ../../views/errors/error500.html");
        exit();
    }
}


/************************* VALIDACION FORMULARIO "MODIFICAR USUARIO"*************************** */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_usuario'])) {

    try {

        /* ===============================
           VALIDACIÓN BÁSICA
        =============================== */

        $idUser = (int) $_POST['idUser'];

        $nombre    = htmlspecialchars(trim($_POST['modif_name'] ?? ''));
        $apellidos = htmlspecialchars(trim($_POST['modif_lastname'] ?? ''));
        $email     = filter_input(INPUT_POST, 'modif_email', FILTER_SANITIZE_EMAIL);
        $telefono  = filter_input(INPUT_POST, 'modif_tel', FILTER_SANITIZE_NUMBER_INT);
        $direccion = htmlspecialchars(trim($_POST['modif_adress'] ?? ''));
        $sexo      = $_POST['modif_sex'] ?? null;
        $rol       = $_POST['modif_rol'] ?? null;

        $errores = validar_actualizacion_registro(
            $nombre,
            $apellidos,
            $email,
            $telefono
        );

        $validacion_fecha = validar_fecha_nacimiento($_POST['modif_date'] ?? null);

        if (!$validacion_fecha['ok']) {
            $errores[] = $validacion_fecha['error'];
        }

        if (!empty($errores)) {
            $_SESSION['mensaje_error'] = implode('<br>', $errores);
            header("Location: ../../views/admin/usuarios_administracion.php");
            exit;
        }

        $fecha_nacimiento = $validacion_fecha['fecha'];

        /* ===============================
           VALIDAR EMAIL
        =============================== */

        $estadoEmail = check_email_modificar(
            $email,
            $idUser,
            $mysqli_connection
        );

        if ($estadoEmail === 'EMAIL_OTRO_USUARIO') {
            $_SESSION['mensaje_error'] =
                "Ya existe otro usuario registrado con ese email.";
            header("Location: ../../views/admin/usuarios_administracion.php");
            exit;
        }

        /* ===============================
           TRANSACCIÓN
        =============================== */

        $mysqli_connection->begin_transaction();

        /* ---------- UPDATE users_data ---------- */
        $stmtData = $mysqli_connection->prepare(
            "UPDATE users_data
             SET nombre = ?, apellidos = ?, email = ?, telefono = ?,
                 fecha_nacimiento = ?, direccion = ?, sexo = ?
             WHERE idUser = ?"
        );

        if (!$stmtData) {
            throw new Exception($mysqli_connection->error);
        }

        $stmtData->bind_param(
            "sssssssi",
            $nombre,
            $apellidos,
            $email,
            $telefono,
            $fecha_nacimiento,
            $direccion,
            $sexo,
            $idUser
        );

        if (!$stmtData->execute()) {
            throw new Exception($stmtData->error);
        }

        $stmtData->close();

        /* ---------- UPDATE users_login ---------- */
        $stmtLogin = $mysqli_connection->prepare(
            "UPDATE users_login SET rol = ? WHERE idUser = ?"
        );

        if (!$stmtLogin) {
            throw new Exception($mysqli_connection->error);
        }

        $stmtLogin->bind_param("si", $rol, $idUser);

        if (!$stmtLogin->execute()) {
            throw new Exception($stmtLogin->error);
        }

        $stmtLogin->close();

        /* ---------- COMMIT ---------- */
        $mysqli_connection->commit();

        $_SESSION['mensaje_exito'] =
            "El usuario se ha modificado correctamente";

        header("Location: ../../views/admin/usuarios_administracion.php");
        exit;

    } catch (Exception $e) {

        if ($mysqli_connection->errno) {
            $mysqli_connection->rollback();
        }

        error_log("Error modificar usuario: " . $e->getMessage());
        header('Location: ../../views/errors/error500.html');
        exit;

    } finally {

        if (isset($mysqli_connection)) {
            $mysqli_connection->close();
        }
    }
}




/************************* VALIDACION FORMULARIO "BORRAR USUARIO"*************************** */

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrar_usuario'])){

    $idUser = $_POST['idUser'];

    if (borrar_usuario($idUser, $mysqli_connection)) {
        $_SESSION['mensaje_exito'] = "El usuario se ha borrado correctamente";

        header("Location: ../../views/admin/usuarios_administracion.php?delete=ok");
    } else {
        # Se redirige al usuario a la página de error 500
        header('Location: ../../views/errors/error500.html');
        exit();
    }

}