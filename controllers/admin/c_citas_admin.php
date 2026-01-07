<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../db_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['rol'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

try {

    /* ===== CREAR ===== */
    if (isset($_POST['crear_cita'])) {

        $idUser = (int)$_POST['idUser'];
        $fecha  = $_POST['fecha_cita'];
        $hora   = $_POST['hora_cita'];
        $motivo = trim($_POST['motivo_cita']);

        if ($idUser <= 0 || !$fecha || !$hora || !$motivo) {
            throw new Exception("Datos inválidos para crear la cita.");
        }

        crear_cita($idUser, $fecha, $hora, $motivo, $mysqli_connection);

        $_SESSION['mensaje_exito'] = "Cita creada correctamente";
    }

    /* ===== MODIFICAR ===== */
    if (isset($_POST['modificar_cita'])) {

        $idCita = (int)$_POST['idCita'];
        $fecha  = $_POST['modif_fecha'];
        $hora   = $_POST['modif_hora'];
        $motivo = trim($_POST['modif_motivo']);

        if ($idCita <= 0 || !$fecha || !$hora || !$motivo) {
            throw new Exception("Datos inválidos para modificar la cita.");
        }

        actualizar_cita($idCita, $fecha, $hora, $motivo, $mysqli_connection);

        $_SESSION['mensaje_exito'] = "Cita modificada correctamente";
    }

    /* ===== BORRAR ===== */
    if (isset($_POST['borrar_cita'])) {

        $idCita = (int)$_POST['idCita'];

        if ($idCita <= 0) {
            throw new Exception("ID de cita inválido.");
        }

        borrar_cita($idCita, $mysqli_connection);

        $_SESSION['mensaje_exito'] = "Cita eliminada correctamente";
    }

} catch (Exception $e) {

    error_log("CITAS ADMIN: " . $e->getMessage());
    $_SESSION['mensaje_error'] = $e->getMessage();
}

header("Location: ../../views/admin/citas_administracion.php");
exit;
