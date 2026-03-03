<?php
# Vinculamos los archivos necesarios
require_once 'db_conn.php';
require_once 'db_functions.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/validations/v_inputData.php';

# Comprobar si existe una sesión activa y en caso de que no así la crearemos
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

# Comprobar si la información llega desde POST y dedse nuestro formulario con submit (iniciar_sesion)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['iniciar_sesion'])){
    # Obtener los datos del fomulario saneados
    $usuario = htmlspecialchars($_POST['user_login_name']);
    $pass = $_POST['user_password'];

    # Validar el formulario a través de la función validar_login()
    $errores_validacion = validar_login($usuario, $pass);

    # Comprobar si se han generado errores de validacion o no
    if(!empty($errores_validacion)){
        # Si hay errores los guardamos en una cadena de caracteres que mostraremos al usuario
        $mensaje_error = "";

        foreach($errores_validacion as $clave => $mensaje){
            $mensaje_error .= $mensaje . "<br>";
        }

        # Asignamos la cadena de caracteres con los erres a $_SESSION['mensaje_error']
        $_SESSION['mensaje_error'] = $mensaje_error;
        header("Location: ../views/login.php");
        exit();
    }

    // Intetamos comprobar el inicio de sesión
    try{
        # Inicializamos una variable para guardar los errores de excepcion posibles
        $exception_error = false;

        $user = get_user_by_username($usuario, $mysqli_connection, $exception_error);

        # Comprobar si se ha capturado alguna excepción
        if($exception_error){
            # Redirigimos a la página de error que tengamos configurada
            $_SESSION['mensaje_error'] = "Error al buscar el usuario. Inténtelo de nuevo más tarde o si le sigue sucediendo contacte con el equipo de soporte";
            header("Location: ../../error500.html");
            exit();
        }
        
        # Comprobar si hemos encontrado al usuario
        if($user){
            # Verificar si la contraseña faciltiada por el usuario en el formulario coincide con la de la BBDD
            if(password_verify($pass, $user['contrasena'])){
                
                # Establecer las variables de sesión y redirigir al usuario 
                $_SESSION['user_data'] = [
                    'id_user' => $user['idUser'],
                    'username' => $user['usuario'],
                    'rol'  => $user['rol']
                ];

                header('Location: ../index.php');
                exit();
            
            }else{
                # Si la contraseña no coincide, establecemos un mensaje de error
                $_SESSION['mensaje_error'] = "La contraseña no es correcta";
                header("Location: ../views/login.php");
                exit();
            }
        }else{
            # Si no se encuentra el usuario o no existe, establecemos un mensaje de error
            $_SESSION['mensaje_error'] = "No se encontro un usuario con ese nombre de usuario";
            header("Location: ../views/login.php");
            exit();
        }


    }catch(Exception $e){
        error_log("Error durante el proceso de inicio de sesión: " . $e -> getMessage());
        header("Location: ../views/errors/error500.html");
        exit();
    
    }finally{
        # Cerrar la conexión a la base de datos si aún sigue abierta
        if(isset($mysqli_connection) && ($mysqli_connection)){
            $mysqli_connection -> close();
        }
    }


}



?>