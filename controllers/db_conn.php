<?php
# Incluir/vincular los parámetros de conexión
require_once '.env.php';

# Vinculamos la ruta absoluta al directorio config.php desde db_conn.php
require_once __DIR__ . '/../config/config.php';

# Definimos una función para realizar la conexión a la BBDD
function connectToDatabase(){
    # Crear una variable de conexión
    static $mysqli_conn = null;

    if($mysqli_conn === null){
        try{
            # Crear la conexión a la BBDD
            $mysqli_conn = new mysqli(SERVER_HOST, USER, PASSWORD, DATABASE_NAME);

            # Comprobar que la conexión se haya realizado correctamente
            if($mysqli_conn -> connect_errno){
                # Registrar el error en el archivo log
                error_log("Fallo al conecar a la base de datos: " . $mysqli_conn -> connect_error);
                return null;
            }
        } catch(Exception $e){
            # Registrar la exepción en el archivo log
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            return null;
        }

    }

    return $mysqli_conn;

}

$mysqli_connection = connectToDatabase(); // $mysqli_conn o null

if($mysqli_connection === null){
    header('Location: ./views/errors/error500.html');
    exit();
}


?>