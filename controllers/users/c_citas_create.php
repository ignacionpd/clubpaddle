<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../db_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =============================
   1. SEGURIDAD BÁSICA
============================= */

if (
    !isset($_SESSION['user_data']) ||
    $_SESSION['user_data']['rol'] !== 'user'
) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['crear_cita'])) {
    header("Location: ../../views/users/citas.php");
    exit;
}

/* =============================
   2. RECOGER DATOS
============================= */

$idUser = $_SESSION['user_data']['id_user'];
$fecha  = $_POST['fecha_cita'] ?? '';
$hora   = $_POST['hora_cita'] ?? '';
$motivo = trim($_POST['motivo_cita'] ?? '');

/* =============================
   3. VALIDACIONES PHP
============================= */

$errores = validar_cita($fecha, $hora, $motivo);

if (!empty($errores)) {
    $_SESSION['mensaje_error'] = implode('<br>', $errores);
    header("Location: ../../views/users/citas.php");
    exit;
}

/* =============================
   4. BD + LÓGICA DE NEGOCIO
============================= */

try {
    $mysqli = connectToDatabase();

    if (!$mysqli) {
        throw new Exception("Error de conexión a la base de datos.");
    }

    /* --------------------------------------------------
       4.1 COMPROBAR DISPONIBILIDAD (MAX 8 / HORA)
    -------------------------------------------------- */

    $stmt = $mysqli->prepare(
        "SELECT COUNT(*) AS total
         FROM citas
         WHERE fecha_cita = ?
           AND hora_cita = ?"
    );

    if (!$stmt) {
        throw new Exception("Error al preparar consulta de disponibilidad.");
    }

    $stmt->bind_param("ss", $fecha, $hora);
    $stmt->execute();

    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($resultado['total'] >= 8) {
        $_SESSION['mensaje_error'] =
            "No hay disponibilidad para la fecha y hora seleccionadas.";
        header("Location: ../../views/users/citas.php");
        exit;
    }

    /* --------------------------------------------------
       4.2 INSERTAR CITA
    -------------------------------------------------- */

    $stmt = $mysqli->prepare(
        "INSERT INTO citas (idUser, fecha_cita, hora_cita, motivo_cita)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception("Error al preparar INSERT.");
    }

    $stmt->bind_param("isss", $idUser, $fecha, $hora, $motivo);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar INSERT.");
    }

    $stmt->close();

    $_SESSION['mensaje_exito'] = "Cita solicitada correctamente.";

} catch (Exception $e) {

    error_log("Error al crear cita: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "No se pudo crear la cita.";

}

/* =============================
   5. REDIRECCIÓN FINAL
============================= */

header("Location: ../../views/users/citas.php");
exit;
