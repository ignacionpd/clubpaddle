<?php
# Vinculamos los archivos necesarios
require_once 'db_conn.php';
require_once 'db_functions.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/validations/v_inputData.php';

# Comprobamos si existe una sesión activa y en caso de que no sea así la creamos.
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

# Comprobamos si la información llega a través del método POST y del formulario con submit "registrarse"
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrarse'])){
    # En primer lugar obtenemos los datos del formulario saneados
    $nombre = htmlspecialchars($_POST['user_name']);
    $apellidos = htmlspecialchars($_POST['user_lastname']);
    $email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'user_tel', FILTER_SANITIZE_NUMBER_INT);
    
    # Validamos "fecha de nacimiento" (tipo input date)
    $fecha_nacimiento = validar_fecha_nacimiento($_POST['user_date'] ?? null);  
    /* Si ESTA OK la fecha, nos devuelve un array con la CLAVE "ok" en TRUE, y sino en FALSE.    
        -> Si está en TRUE, recogemos la fecha convertida en string (compatible con BD) con la clave $fecha_nacimiento[fecha]
        -> Si está con FALSE, recogemos el ERROR con $fecha_nacimiento[error];
    */

    $direccion = htmlspecialchars($_POST['user_adress']);
    $sexo =  $_POST['user_sex'];
    $usuario = htmlspecialchars($_POST['user_login_name']);
    $pass = $_POST['user_password'];
        
    # Validar el formulario a través de la función validar_registro() del controlador "v_inputData"
    $errores_validacion = validar_registro($nombre, $apellidos, $email, $telefono,  $usuario, $pass);
     
    # Comprobar si se han generado errores de validacion o no
    if(!empty($errores_validacion) || !$fecha_nacimiento['ok']){
        # Si hay errores de validación vamos a guardarlos en una cadena para mostrarselos al usuario
        $mensaje_error = "";

        # Recorremos el array de errores_validación para concatenar los mensajes en la variable $mensaje_error
        foreach($errores_validacion as $clave => $mensaje){
            $mensaje_error .= $mensaje . "<br>";
        }

        # Si el array fecha_nacimiento tiene la clave "ok" como "false", concatenamos también a la variable $mensaje_error, el valor de su clave "error" que tiene que tener dentro
        if(!$fecha_nacimiento['ok']){
            $mensaje_error .= $fecha_nacimiento['error'];
        }

        # Asignamos la cadena de errores a $_SESSION['mensaje_error']
        $_SESSION['mensaje_error'] = $mensaje_error;

        header("Location: ../views/registro.php");
        exit();
    }

    
    $pass_hash = password_hash($pass, PASSWORD_BCRYPT);

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
            header("Location: ../views/registro.php");
            exit(); 

        # SI el resultado de check_email() y check_usuario() es FALSE    
        }else{
            # SI se produjo una excepción durante el proceso de comprobación
            if($exception_error_email == true || $exception_error_usuario == true){
                # Se redirige al usuario a la página de error 500
                header('Location: ../views/errors/error500.html');
                exit(); 
            # SI el usuario NO existe
            }else{
                # Se prepara la sentecia SQL para realizar la inserción
                $insert_stmt_users_data = $mysqli_connection -> prepare("INSERT INTO users_data(nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                # SI la sentencia NO se ha podido preparar
                if(!$insert_stmt_users_data){
                    # Se guarda el error de preparación de la sentencia
                    error_log("No se pudo preparar la sentencia " . $mysqli_connection -> error);
                    
                    # Se redirige al usuario a la página de error 500
                    header('Location: ../views/errors/error500.html');
                    exit();
                # SI la sentencia se ha podido preparar   
                }else{
                    # Vinculamos los valores introducidos por el usuario a los valores de la sentencia de inserción
                    $insert_stmt_users_data->bind_param("sssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo);

                    # SI la sentencia se ha podido ejecutar
                    if($insert_stmt_users_data -> execute()){

                        # Registramos el ID creado en la última inserción, para luego registrar en la tabla "users_login"
                        $id_user = mysqli_insert_id($mysqli_connection);

                        # Cerramos la sentencia
                        $insert_stmt_users_data -> close();

                          
                    # SI NO se ha podido ejecutar la sentencia    
                    }else{
                        # Se guarda el error de ejecución en el error_log
                        error_log("Error: " . $insert_stmt_users_data -> error);
                        # Redirigimos al usuario a la página de registro
                        header('Location: ../views/errors/error500.html');
                        exit();
                    }
                }



                # Se prepara la sentecia SQL para realizar la inserción en USERS_LOGIN
                $insert_stmt_users_login = $mysqli_connection -> prepare("INSERT INTO users_login(idUser, usuario, contrasena, rol) VALUES (?, ?, ?, ?)");
                
                # SI la sentencia NO se ha podido preparar
                if(!$insert_stmt_users_login){
                    # Se guarda el error de preparación de la sentencia
                    error_log("No se pudo preparar la sentencia " . $mysqli_connection -> error);
                    
                    #Eliminamos el último registro en USERS_DATA
                    $exception_error_registro = false;
                    eliminar_registro($id_user, $mysqli_connection, $exception_error_registro);

                    # Se redirige al usuario a la página de error 500
                    header('Location: ../views/errors/error500.html');
                    exit();

                # SI la sentencia se ha podido preparar   
                }else{

                    $rol = 'user';
                    # Vinculamos los valores introducidos por el usuario a los valores de la sentencia de inserción
                    $insert_stmt_users_login->bind_param("isss",$id_user,$usuario, $pass_hash, $rol);

                    # SI la sentencia se ha podido ejecutar
                    if($insert_stmt_users_login -> execute()){
                        # Cerramos la sentencia
                        $insert_stmt_users_login -> close();

                        
                        # Configuramos un mensaje de éxito para el usuario y le redirigimos a la página de registro.
                        $_SESSION['mensaje_exito'] = "EXITO: El usuario se ha registrado correctamente";
                        header("Location: ../views/registro.php");
                        exit();
                        
                    # SI NO se ha podido ejecutar la sentencia    
                    }else{
                        # Se guarda el error de ejecución en el error_log
                        error_log("Error: " . $insert_stmt_users_login -> error);

                        #Eliminamos el último registro en USERS_DATA
                        $exception_error_registro = false;
                        eliminar_registro($id_user, $mysqli_connection, $exception_error_registro);

                        # Se redirige al usuario a la página de error 500
                        header('Location: ../views/errors/error500.html');
                        exit();
                    }
                }
                
            }

        }
    # SI durante el proceso surge una excepción    
    }catch(Exception $e){
        # Registramos la excepción en el error_log
        error_log("Error en c_registro.php" . $e -> getMessage());
        # Redirigimos al usuario a la página de error 500
        header('Location: ../views/errors/error500.html');
    
    # Independientemente de si se genera una excepción o no al final siempre se realizará el siguiente código
    }finally{
        # Cerramos la consulta si aún sigue abierta
        if(isset($insert_stmt_users_data) && ($insert_stmt_users_data) && isset($insert_stmt_users_login) && ($insert_stmt_users_login)){
            $insert_stmt_users_data -> close();
            $insert_stmt_users_login -> close();
        }

        # Cerramos la conexión a la base de datos si aún sigue abierta
        if(isset($mysqli_connection) && ($mysqli_connection)){
            $mysqli_connection -> close();
        }

    }
}


?>