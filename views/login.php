<?php
require_once __DIR__ . '/../config/config.php';
# Comprobar si existe una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprobamos si ya hay usuario logueado y lo redirigimos al index
if (isset($_SESSION['user_data'])) {
    header(header: 'Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Paddle</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <!-- FAVICON -->
    <link rel="icon" href="../assets/css/images/favicon.png" type="image/png">
</head>

<body>
    <div class="mi_contenedor">
        <!-- HEADER -->
        <header class="mi_encabezado">
            <h1>Club Paddle</h1>
            <nav class="navigationBar">
                <ul class="navigationBarList">
                    <li>
                        <a class="enlace" href="../index.php">Inicio</a>
                    </li>
                    <li>
                        <a class="enlace" href="./noticias.php">Noticias</a>
                    </li>
                    <li>
                        <a class="enlace" href="./registro.php">Registro</a>
                    </li>
                    <li>
                        <a class="enlace active" href="#">Login</a>
                    </li>
                </ul>
            </nav>
        </header>
        <!-- CUERPO PRINCIPAL-->
        <main class="mi_principal">
            <section>
                <h2>Login</h2>

                <div class="aviso_registro">
                    <?php
                    # Comprobar si hay mensajes de error
                    if (isset($_SESSION["mensaje_error"])) {
                        echo "<span class='error_message'>" . $_SESSION['mensaje_error'] . "</span>";

                        # Eliminar el mensaje de error
                        unset($_SESSION["mensaje_error"]);
                    }
                    ?>
                </div>

                <form class="register_login_form" id="login_form" action="../controllers/c_login.php" method="POST">
                    <h3>Inicio de sesión</h3>
                    <div class="form_options">
                        <label for="user_login_name">Usuario:</label>
                        <div class="input_zone">
                            <input type="text" id="user_login_name" name="user_login_name" placeholder="Escriba su nombre de usuario..." title="El nombre de usuario registrado para inicio de sesión (no email)">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_password">Contraseña: </label>
                        <div class="input_zone">
                            <input type="password" id="user_password" name="user_password" placeholder="Escriba su contraseña...">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="password_show">
                        <label for="check_password">
                            <input type="checkbox" id="check_password">
                            Mostrar contraseña
                        </label>
                        <a href="./registro.php">Registrarse</a>
                    </div>
                    <div class="form_buttons">
                        <input type="reset" class="btn_reset" value="Borrar">
                        <input type="submit" class="btn_enviar" name="iniciar_sesion" value="Enviar">
                    </div>

                </form>

            </section>
        </main>
        <!-- PIE DE PÁGINA-->
        <footer class="mi_pie">
            <div class="aviso_legal">
                <small>
                    &copy; Club Paddle - Todos los derechos reservados
                </small>
            </div>
        </footer>
    </div>

    <script src="../assets/scripts/v_login.js"></script>
    <script src="../assets/scripts/show_password.js"></script>
</body>

</html>