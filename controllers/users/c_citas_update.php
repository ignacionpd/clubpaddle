<?php
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../db_functions.php';
require_once __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =============================
   1. SEGURIDAD BÁSICA
============================= */

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['rol'] !== 'user') {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['editar_cita'])) {
    header("Location: ../../views/users/citas.php");
    exit;
}

/* =============================
   2. RECOGER DATOS
============================= */

$idCita = (int) ($_POST['idCita'] ?? 0);
$fecha  = $_POST['fecha_cita'] ?? '';
$hora   = $_POST['hora_cita'] ?? '';
$motivo = trim($_POST['motivo_cita'] ?? '');

$idUser = $_SESSION['user_data']['id_user'];

/* =============================
   3. VALIDACIONES PHP
============================= */

$errores = validar_cita($fecha, $hora, $motivo );

if (!empty($errores)) {
    $_SESSION['mensaje_error'] = implode('<br>', $errores);
    header("Location: ../../views/users/citas.php");
    exit;
}

/* =============================
   4. LÓGICA DE NEGOCIO + BD
============================= */

try {
    $mysqli = connectToDatabase();

    if (!$mysqli) {
        throw new Exception("Error de conexión a la base de datos.");
    }

    /* --------------------------------------------------
       4.1 COMPROBAR DISPONIBILIDAD (MÁX 8 CITAS / HORA)
       👉 ESTA ES LA PARTE QUE TE GENERABA DUDA
    -------------------------------------------------- */

    $stmt = $mysqli->prepare(
        "SELECT COUNT(*) AS total
         FROM citas
         WHERE fecha_cita = ?
           AND hora_cita = ?
           AND idCita != ?"
    );

    if (!$stmt) {
        throw new Exception("Error al preparar consulta de disponibilidad.");
    }

    $stmt->bind_param("ssi", $fecha, $hora, $idCita);
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
       4.2 ACTUALIZAR LA CITA (UPDATE)
    -------------------------------------------------- */

    $stmt = $mysqli->prepare(
        "UPDATE citas
         SET fecha_cita = ?, hora_cita = ?, motivo_cita = ?
         WHERE idCita = ?
           AND idUser = ?
           AND fecha_cita >= CURDATE()"
    );

    if (!$stmt) {
        throw new Exception("Error al preparar UPDATE.");
    }

    $stmt->bind_param("sssii", $fecha, $hora, $motivo, $idCita, $idUser);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la actualización.");
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No se puede modificar una cita pasada.");
    }

    $stmt->close();

    $_SESSION['mensaje_exito'] = "Cita modificada correctamente.";

} catch (Exception $e) {

    error_log("Error al modificar cita: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "No se pudo modificar la cita.";

}

/* =============================
   5. REDIRECCIÓN FINAL
============================= */

header("Location: ../../views/users/citas.php");
exit;