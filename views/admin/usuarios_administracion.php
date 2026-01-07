<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/db_conn.php';
require_once __DIR__ . '/../../controllers/db_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$mysqli = connectToDatabase();
$usuarios = obtener_todos_los_usuarios($mysqli);

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
                        <a class="enlace" href="../../index.php">Inicio</a>
                    </li>
                    <li>
                        <a class="enlace" href="../noticias.php">Noticias</a>
                    </li>
                    <li>
                        <a class="enlace  active" href="#">Usuarios administración</a>
                    </li>
                    <li>
                        <a class="enlace" href="./citas_administracion.php">Citas administración</a>
                    </li>
                    <li>
                        <a class="enlace" href="./noticias_administracion.php">Noticias administración</a>
                    </li>
                    <li>
                        <a class="enlace" href="./admin_profile.php">Perfil</a>
                    </li>
                    <li>
                        <a class="enlace" href="../../controllers/logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </nav>
        </header>
        <!-- MAIN -->
        <main class="mi_principal">

            <h2>Administración de usuarios</h2>

            <section>
                <div class="aviso_registro">
                    <?php
                    # Comprobar si hay mensajes de error
                    if (isset($_SESSION["mensaje_error"])) {
                        echo "<span class='error_message'>" . $_SESSION['mensaje_error'] . "</span>";

                        # Eliminar el mensaje de error
                        unset($_SESSION["mensaje_error"]);
                    }


                    # Comprobar si hay mensajes de exito
                    if (isset($_SESSION["mensaje_exito"])) {
                        echo "<span class='success_message'>" . $_SESSION['mensaje_exito'] . "</span>";

                        # Eliminar el mensaje de error
                        unset($_SESSION["mensaje_exito"]);
                    }

                    ?>
                </div>

                <form class="admin_usuario_create" id="admin_register_form" action="../../controllers/admin/c_usuarios_admin.php" method="post">

                    <h3>Registrar nuevo usuario</h3>

                    <div class="form_options">
                        <label for="user_name">Nombre: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_name" name="user_name" placeholder="Escriba el nombre..." title="El nombre deberá contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_lastname">Apellidos: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_lastname" name="user_lastname" placeholder="Escriba sus apellidos..." title="Los apellidos deberán contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_email">Email: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_email" name="user_email" placeholder="Escriba un correo electrónico..." title="El correo electrónico similar a: xxxxx@xxx.xxx">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_tel">Teléfono: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_tel" name="user_tel" placeholder="Escriba el número de teléfono..." title="El telefono deberá contener 9 dígitos">
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_options">
                        <label for="user_date">Fecha de nacimiento: *</label>
                        <div class="input_zone">
                            <input type="date" id="user_date" name="user_date" title="Seleccione fecha de nacimiento (debe ser mayor de edad)">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_adress">Dirección:</label>
                        <div class="input_zone">
                            <input type="text" id="user_adress" name="user_adress" placeholder="Escriba su dirección..." title="La dirección deberá contener entre 3 y 45 letras">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_sex">Sexo:</label>
                        <div class="input_zone">
                            <select name="user_sex">
                                <option value="">Seleccione</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                            </select>
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_login_name">Usuario: *</label>
                        <div class="input_zone">
                            <input type="text" id="user_login_name" name="user_login_name" placeholder="Escriba un nombre de usuario..." title="El nombre de usuario deberá contener entre 6 y 10 caracteres alfanuméricos">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_rol">Rol *</label>
                        <div class="input_zone">
                            <select name="user_rol">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="form_options">
                        <label for="user_password">Contraseña: *</label>
                        <div class="input_zone">
                            <input type="password" id="user_password" name="user_password" placeholder="Escriba una contraseña..." title="La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)">
                            <small class="input_error"></small>
                        </div>
                    </div>
                    <div class="password_show">
                        <label for="check_password">
                            <input type="checkbox" id="check_password">
                            Mostrar contraseña
                        </label>
                    </div>
                    <div class="form_buttons">
                        <input type="reset" class="btn_reset" value="Borrar">
                        <input type="submit" class="btn_enviar" name="registro_admin" value="Enviar">
                    </div>

                </form>

            </section>

            <hr>

            <section>

                <h3>Usuarios registrados</h3>
                <div id="admin_users_container">
                    <?php foreach ($usuarios as $u): ?>

                        <div class="admin_user_card">

                            <!-- ===== VISTA ===== -->
                            <div class="admin_user_view">

                                <p><strong><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?></strong></p>
                                <p><strong>Email: </strong><?= htmlspecialchars($u['email']) ?></p>
                                <p><strong>Usuario: </strong><?= htmlspecialchars($u['usuario']) ?></p>
                                <p><strong>Teléfono: </strong><?= htmlspecialchars($u['telefono']) ?></p>
                                <p><strong>Fecha nacimiento: </strong><?= date('d-m-Y', strtotime($u['fecha_nacimiento'])) ?></p>
                                <p><strong>Sexo: </strong><?= htmlspecialchars($u['sexo'] ?? '—') ?></p>
                                <p><strong>Dirección: </strong><?= htmlspecialchars($u['direccion'] ?? '—') ?></p>
                                <p><strong>Rol: </strong><?= htmlspecialchars($u['rol']) ?></p>

                                <div class="admin_user_actions">
                                    <button type="button" class="btn_modificar_usuario">Modificar</button>
                                    <form method="post" action="../../controllers/admin/c_usuarios_admin.php">
                                        <input type="hidden" name="idUser" value="<?= $u['idUser'] ?>">
                                        <button class="btn_delete" name="borrar_usuario">Borrar</button>
                                    </form>
                                </div>
                            </div>

                            <!-- ===== EDICIÓN ===== -->
                            <div class="admin_user_edit hidden">

                                <form action="../../controllers/admin/c_usuarios_admin.php"
                                    method="post"
                                    class="admin_user_form">

                                    <input type="hidden" name="idUser" value="<?= $u['idUser'] ?>">

                                    <div class="admin_user_row">
                                        <label>Nombre</label>
                                        <div class="input_zone">
                                            <input type="text" name="modif_name" value="<?= $u['nombre'] ?>">
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Apellidos</label>
                                        <div class="input_zone">
                                            <input type="text" name="modif_lastname" value="<?= $u['apellidos'] ?>">
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Email</label>
                                        <div class="input_zone">
                                            <input type="email" name="modif_email" value="<?= $u['email'] ?>">
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Teléfono</label>
                                        <div class="input_zone">
                                            <input type="text" name="modif_tel" value="<?= $u['telefono'] ?>">
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Fecha nacimiento</label>
                                        <div class="input_zone">
                                            <input type="date"
                                                name="modif_date"
                                                value="<?= $u['fecha_nacimiento'] ?>"
                                                max="<?= date('Y-m-d') ?>">
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Dirección</label>
                                        <div class="input_zone">
                                            <input type="text" name="modif_adress" value="<?= $u['direccion'] ?? '' ?>">
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Sexo</label>
                                        <div class="input_zone">
                                            <select name="modif_sex">
                                                <option value="Hombre" <?= $u['sexo'] === 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                                                <option value="Mujer" <?= $u['sexo'] === 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                                            </select>
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_row">
                                        <label>Rol</label>
                                        <div class="input_zone">
                                            <select name="modif_rol">
                                                <option value="user" <?= $u['rol'] === 'user' ? 'selected' : '' ?>>User</option>
                                                <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <small class="input_error"></small>
                                        </div>
                                    </div>

                                    <div class="admin_user_actions">
                                        <button type="submit" class="admin_btn_enviar" name="modificar_usuario">
                                            Guardar cambios
                                        </button>

                                        <button type="button" class="admin_btn_cancel">
                                            Cancelar
                                        </button>
                                    </div>

                                </form>
                            </div>

                        </div>


                    <?php endforeach; ?>
                </div>
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
    <script src="../../assets/scripts/v_usuarios_admin.js"></script>

</body>

</html>