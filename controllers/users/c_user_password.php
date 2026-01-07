<?php
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../../config/config.php';
include_once __DIR__ . '/../../controllers/validations/v_inputData.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔐 Verificar usuario logueado
if (!isset($_SESSION['user_data'])) {
    header("Location: ../../index.php");
    exit;
}

// 🔐 Verificar envío del formulario
if (!isset($_POST['cambiar_contrasena'])) {
    header("Location: ../../views/users/user_profile.php");
    exit;
}

$password  = $_POST['password']  ?? '';
$password2 = $_POST['password2'] ?? '';

// Verificar si la contraseña ingresada es válida y coincide con la repetición
$error_validacion_password = validar_contrasena($password, $password2);

// Si el campo $error no está vacío, cargamos su contenido en la $_SESSION['mensaje_error']
if (!empty($error_validacion_password)) {
    $_SESSION['mensaje_error'] = $error_validacion_password;
    header("Location: ../../views/users/user_profile.php");
    exit;
}

try {
    $mysqli = connectToDatabase();

    if (!$mysqli) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }

    $idUser = $_SESSION['user_data']['id_user'];
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "
        UPDATE users_login
        SET contrasena = ?
        WHERE idUser = ?
    ";

    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta.");
    }

    $stmt->bind_param("si", $hash, $idUser);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la actualización.");
    }

    $stmt->close();

    $_SESSION['mensaje_exito'] = "La contraseña se ha actualizado correctamente.";

} catch (Exception $e) {
    error_log("Error al cambiar contraseña: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "No se pudo actualizar la contraseña.";
}

header("Location: ../../views/users/user_profile.php");
exit;