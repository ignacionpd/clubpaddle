<?php

# Declaramos como constantes las expresiones regulares que van a filtrar o comprobar los datos
define("NOMBRE_REGEX", "/^[a-zA-Z ]{2,45}$/");
define(constant_name: "TELEFONO_REGEX", value:"/^\d{9}$/");
//define("DIRECCION_REGEX",  "/^(?=.{8,50}$)[A-Za-z0-9º\-,]+( [A-Za-z0-9º\-,]+){0,5}$/u");
define("USUARIO_REGEX", "/^[a-zA-Z0-9]{6,10}$/");
define("CONTRASENA_REGEX", "/^(?=.*[A-Z])(?=.*\d)(?=.*[.,_\-])[a-zA-Z\d.,_\-]{4,10}$/");

function validar_fecha_nacimiento(?string $fecha_input): array
{
    if (empty($fecha_input)) {
        return [
            'ok' => false,
            'error' => '- La fecha de nacimiento es obligatoria.'
        ];
    }

    $fecha = DateTime::createFromFormat('Y-m-d', $fecha_input);
    $errores = DateTime::getLastErrors();

    if (
        $fecha === false ||
        ($errores !== false && (
            $errores['warning_count'] > 0 ||
            $errores['error_count'] > 0
        ))
    ) {
        return [
            'ok' => false,
            'error' => '- La fecha introducida no es válida.'
        ];
    }

    $hoy = new DateTime();

    if ($fecha > $hoy) {
        return [
            'ok' => false,
            'error' => '- La fecha de nacimiento no puede ser futura.'
        ];
    }

    $edad = $fecha->diff($hoy)->y;

    if ($edad < 18) {
        return [
            'ok' => false,
            'error' => '- Debes ser mayor de edad para registrarte.'
        ];
    }

    return [
        'ok' => true,
        'fecha' => $fecha->format('Y-m-d')
    ];
}


# Definimos la función validar_registro()
function validar_registro($nombre, $apellidos, $email, $telefono, $usuario, $pass){
    # Declarar un array asociativo
    $errores = [];

    # Validación del nombre haciendo uso de la constante NOMBRE_REGEX
    if(!preg_match(NOMBRE_REGEX, $nombre)){
        $errores['nombre'] = "- El nombre deberá contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto";
    }

     # Validación de los apellidos haciendo uso de la constante NOMBRE_REGEX
    if(!preg_match(NOMBRE_REGEX, $apellidos)){
        $errores['apellidos'] = "- Los apellidos deberán contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto";
    }

    # Validación del correo electrónico
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errores['email'] = "- El formato del correo electrónico no es válido";
    }

    if(!preg_match(TELEFONO_REGEX, $telefono)){
        $errores['telefono'] = "- El teléfono debe contener 9 dígitos";
    }

    /* # Validación de la dirección haciendo uso de la constante DIRECCION_REGEX
    if(!preg_match(DIRECCION_REGEX, $direccion)){
        $errores['direccion'] = "- La dirección deberá contener entre 8 y 50 caracteres, y puede tener hasta 5 espacios (no consecutivos) y sólo podrá contener los símbolos 'º', '-' y ','";
    }*/

    # Validación del nombre de usuario haciendo uso de la constante NOMBRE_REGEX
    if(!preg_match(USUARIO_REGEX, $usuario)){
        $errores['usuario'] = "- El nombre de usuario deberá contener entre 6 y 10 caracteres alfanuméricos";
    }

    # Validación de la contraseña haciendo uso de la constante CONTRASENA_REGEX
    if(!preg_match(CONTRASENA_REGEX, $pass)){
        $errores['pass'] = "- La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)";
    }

    return $errores;

}

# Definimos la función validar_login()
function validar_login($usuario, $pass){
    # Declarar un array asociativo
    $errores = [];

    # Validación del correo electrónico
    if(!preg_match(USUARIO_REGEX, $usuario)){
        $errores['usuario'] = "- El nombre de usuario deberá contener entre 6 y 10 caracteres alfanuméricos";
    }

    # Validación de la contraseña haciendo uso de la constante CONTRASENA_REGEX
    if(!preg_match(CONTRASENA_REGEX, $pass)){
        $errores['pass'] = "- La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)";
    }

    return $errores;
}


/************************USER_PROFILE************************** */
# Validación de actualización de datos del usuario
function validar_perfil($nombre, $apellidos, $email, $telefono) {

    $errores = [];

    if (!preg_match(NOMBRE_REGEX, $nombre)) {
        $errores['nombre'] = "- El nombre no es válido.";
    }

    if (!preg_match(NOMBRE_REGEX, $apellidos)) {
        $errores['apellidos'] = "- Los apellidos no son válidos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "- El email no tiene un formato válido.";
    }

    if (!preg_match(TELEFONO_REGEX, $telefono)) {
        $errores['telefono'] = "- El teléfono debe contener 9 dígitos.";
    }

    return $errores;
}

// Validar actualización de contraseña
function validar_contrasena($password, $password2) {
    
    $error = "";

    # Validación de la contraseña haciendo uso de la constante CONTRASENA_REGEX
    if(!preg_match(CONTRASENA_REGEX, $password)){

        $error = "La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)";
    
    }else if(empty($password) || empty($password2)){ // Verificar Contraseñas vacías
        
        $error = "Debes completar ambos campos de contraseña.";
        
    }else {

        // Verificar contraseñas distintas
        if ($password !== $password2) {
            $error = "Las contraseñas no coinciden.";
        }
    }

    return $error;
}




/*************************ADMINISTRADOR********************/

// Definimos la función para validar los datos ingresados en "MODIFICAR USUARIO"
function validar_actualizacion_registro($nombre, $apellidos, $email, $telefono){
    # Declarar un array asociativo
    $errores = [];

    # Validación del nombre haciendo uso de la constante NOMBRE_REGEX
    if(!preg_match(NOMBRE_REGEX, $nombre)){
        $errores['nombre'] = "- El nombre deberá contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto";
    }

     # Validación de los apellidos haciendo uso de la constante NOMBRE_REGEX
    if(!preg_match(NOMBRE_REGEX, $apellidos)){
        $errores['apellidos'] = "- Los apellidos deberán contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto";
    }

    # Validación del correo electrónico
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errores['email'] = "- El formato del correo electrónico no es válido";
    }

    if(!preg_match(TELEFONO_REGEX, $telefono)){
        $errores['telefono'] = "- El teléfono debe contener 9 dígitos";
    }
  
    return $errores;

}



?>