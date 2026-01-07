<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/db_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_data'])) {
    header('Location: ../../index.php');
    exit;
}

if ($_SESSION['user_data']['rol'] !== 'user') {
    header('Location: ../../index.php');
    exit;
}

#Contultamos si 
$user = consultarDatos();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Paddle</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <!-- FAVICON -->
    <link rel="icon" href="../../assets/css/images/favicon.png" type="image/png">
</head>

<body>
    <div class="mi_contenedor">

        <header class="mi_encabezado">
            <h1>Club Paddle</h1>
            <nav class="navigationBar">
                <ul class="navigationBarList">
                    <li><a class="enlace" href="../../index.php">Inicio</a></li>
                    <li><a class="enlace" href="../noticias.php">Noticias</a></li>
                    <li><a class="enlace" href="./citas.php">Citas</a></li>
                    <li><a class="enlace active" href="#">Perfil</a></li>
                    <li><a class="enlace" href="../../controllers/logout.php">Cerrar sesión</a></li>
                </ul>
            </nav>
        </header>

        <main class="mi_principal">
            <section>

                <h2>Perfil</h2>

                <div class="aviso_registro">
                    <?php
                    if (isset($_SESSION["mensaje_error"])) {
                        echo "<span class='error_message'>{$_SESSION['mensaje_error']}</span>";
                        unset($_SESSION["mensaje_error"]);
                    }
                    if (isset($_SESSION["mensaje_exito"])) {
                        echo "<span class='success_message'>{$_SESSION['mensaje_exito']}</span>";
                        unset($_SESSION["mensaje_exito"]);
                    }
                    ?>
                </div>




                <form class="user_profile" id="user_profile_form" action="../../controllers/users/c_user_profile.php" method="post">

                    <h3>Actualizar datos del perfil</h3>

                    <div class="form_options">
                        <label for="user_name">Nombre: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_name" name="user_name" value="<?= $user['nombre'] ?>" title="El nombre deberá contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_lastname">Apellidos: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_lastname" name="user_lastname" value="<?= $user['apellidos'] ?>" title="Los apellidos deberán contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_email">Email: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_email" name="user_email" value="<?= $user['email'] ?>" title="El correo electrónico similar a: xxxxx@xxx.xxx">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_tel">Teléfono: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_tel" name="user_tel" value="<?= $user['telefono'] ?>" title="El telefono deberá contener 9 dígitos">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_date">Fecha de nacimiento: *</label>
                        <div class="input_zone">
                            <input type="date" id="user_date" name="user_date" value="<?= $user['fecha_nacimiento'] ?>" title="Seleccione su fecha de nacimiento">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_adress">Dirección:</label>
                        <div class="input_zone">
                            <input type="text" id="user_adress" name="user_adress" value="<?= $user['direccion'] ?>">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_sex">Sexo:</label>
                        <div class="input_zone">
                            <select name="user_sex">
                                <option value="Hombre" <?= $user['sexo'] == 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                                <option value="Mujer" <?= $user['sexo'] == 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form_buttons">
                        <button type="submit" class="btn_enviar" name="actualizar_perfil">Actualizar datos</button>
                    </div>
                </form>

                <hr>

                <form class="user_profile" id="user_password_form" action="../../controllers/users/c_user_password.php" method="post">

                    <h3>Cambiar contraseña</h3>

                    <div class="form_options">
                        <label for="password">Nueva contraseña</label>
                        <div class="input_zone">
                            <input id="password" type="password" name="password" title="La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="password2">Repetir contraseña</label>
                        <div class="input_zone">
                            <input id="password2" type="password" name="password2" title="La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_buttons">
                        <button type="submit" class="btn_enviar" name="cambiar_contrasena">Cambiar contraseña</button>
                    </div>
                </form>
    </div>


</main>

<footer class="mi_pie">
    <div class="aviso_legal">
        <small>
            &copy; Club Paddle - Todos los derechos reservados
        </small>
    </div>
</footer>

</div>

<script src="../../assets/scripts/v_update_users.js"></script>
</body>

</html>