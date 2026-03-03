<?php
    require_once __DIR__ . '/../config/config.php';

    // Iniciar la sesión para poder acceder a las variables de sesión
    session_start();

    // Limpiar todas las variables de sesión
    $_SESSION = array();

    // Si se desea destruir la sesión completamente, borra también la cookie de sesión.
    // Nota: ¡Esto destruirá la sesión, y no solo la información de sesión!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir la sesión.
    session_destroy();

    // Redirigir al usuario a la página de inicio de sesión o a la página principal
    header("Location: ../index.php");
    exit();
?>