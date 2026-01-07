<?php
require_once '../../config/config.php';
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../db_functions.php';
require_once __DIR__ . '/../validations/v_inputData.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {

    try {

        /* ===============================
           DATOS BÁSICOS
        =============================== */

        if (empty($_SESSION['user_data']['id_user'])) {
            throw new Exception("Sesión inválida");
        }

        $idUser = (int) $_SESSION['user_data']['id_user'];

        $nombre    = htmlspecialchars(trim($_POST['user_name'] ?? ''));
        $apellidos = htmlspecialchars(trim($_POST['user_lastname'] ?? ''));
        $email     = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
        $telefono  = filter_input(INPUT_POST, 'user_tel', FILTER_SANITIZE_NUMBER_INT);
        $direccion = htmlspecialchars(trim($_POST['user_adress'] ?? ''));
        $sexo      = $_POST['user_sex'] ?? null;

        /* ===============================
          VALIDACIONES
        =============================== */

        $errores = validar_perfil(
            $nombre,
            $apellidos,
            $email,
            $telefono
        );

        $validacion_fecha = validar_fecha_nacimiento($_POST['user_date'] ?? null);

        if (!$validacion_fecha['ok']) {
            $errores[] = $validacion_fecha['error'];
        }

        if (!empty($errores)) {
            $_SESSION['mensaje_error'] = implode('<br>', $errores);
            header("Location: ../../views/users/user_profile.php");
            exit;
        }

        $fecha_nacimiento = $validacion_fecha['fecha'];

        /* ===============================
         VALIDAR EMAIL (3 ESTADOS)
        =============================== */

        $estadoEmail = check_email_modificar(
            $email,
            $idUser,
            $mysqli_connection
        );

        if ($estadoEmail === 'EMAIL_OTRO_USUARIO') {
            $_SESSION['mensaje_error'] =
                "Ese email ya está registrado por otro usuario.";
            header("Location: ../../views/users/user_profile.php");
            exit;
        }

        /* ===============================
           UPDATE PERFIL
        =============================== */

        $stmt = $mysqli_connection->prepare(
            "UPDATE users_data
             SET nombre = ?, apellidos = ?, email = ?, telefono = ?,
                 fecha_nacimiento = ?, direccion = ?, sexo = ?
             WHERE idUser = ?"
        );

        if (!$stmt) {
            throw new Exception($mysqli_connection->error);
        }

        $stmt->bind_param(
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

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $stmt->close();

        $_SESSION['mensaje_exito'] =
            "Perfil actualizado correctamente.";

        header("Location: ../../views/users/user_profile.php");
        exit;

    } catch (Exception $e) {

        error_log("Error actualizar perfil usuario: " . $e->getMessage());
        header('Location: ../../views/errors/error500.html');
        exit;

    } finally {

        if (isset($mysqli_connection)) {
            $mysqli_connection->close();
        }
    }
}
