<?php
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['rol'] !== 'user') {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_POST['borrar_cita'], $_POST['idCita'])) {
    header("Location: ../../views/users/citas.php");
    exit;
}

$idCita = (int) $_POST['idCita'];
$idUser = $_SESSION['user_data']['id_user'];

try {
    $mysqli = connectToDatabase();

    if (!$mysqli) {
        throw new Exception("Error de conexión");
    }

    // Solo borrar si es del usuario y no está en el pasado
    $stmt = $mysqli->prepare(
        "DELETE FROM citas
         WHERE idCita = ?
           AND idUser = ?
           AND fecha_cita >= CURDATE()"
    );

    if (!$stmt) {
        throw new Exception("Error al preparar DELETE");
    }

    $stmt->bind_param("ii", $idCita, $idUser);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar DELETE");
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No se puede borrar una cita pasada o inexistente");
    }

    $stmt->close();

    $_SESSION['mensaje_exito'] = "Cita eliminada correctamente.";

} catch (Exception $e) {
    error_log("Error al borrar cita: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "No se pudo eliminar la cita.";
}

header("Location: ../../views/users/citas.php");
exit;