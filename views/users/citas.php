<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/db_conn.php';
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

$id_user = $_SESSION['user_data']['id_user'];

#Contultamos si 
$citasActuales = obtener_citas_vigentes_user($id_user, $mysqli_connection);
$pastCitas = obtener_citas_pasadas_user($id_user, $mysqli_connection);

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
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="../../assets/images/favicon/site.webmanifest">
</head>

<body>
    <div class="mi_contenedor">
        <!-- HEADER -->
        <header class="mi_encabezado">
            <h1>Club Paddle</h1>
            <nav class="navigationBar">

                <ul class="navigationBarList">
                    <label class="label_check" id="menuToggle">
                        <span class="icon-menu"></span>
                    </label>
                    <li><a class="enlace" href="../../index.php">Inicio</a></li>
                    <li><a class="enlace" href="../noticias.php">Noticias</a></li>
                </ul>

                <!-- MENÚ DESPLEGABLE -->
                <ul class="navigationBarListUser">
                    <li><a class="enlace active" href="#">Citas</a></li>
                    <li><a class="enlace" href="./user_profile.php">Perfil</a></li>
                    <li><a class="enlace" href="../../controllers/logout.php">Cerrar sesión</a></li>
                </ul>
            </nav>
        </header>

        <!-- MAIN -->
        <main class="mi_principal">

            <h2>Citas</h2>

            <!-- MENSAJES -->
            <div class="aviso_registro">
                <?php if (isset($_SESSION['mensaje_error'])): ?>
                    <span class="error_message"><?= $_SESSION['mensaje_error'] ?></span>
                    <?php unset($_SESSION['mensaje_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['mensaje_exito'])): ?>
                    <span class="success_message"><?= $_SESSION['mensaje_exito'] ?></span>
                    <?php unset($_SESSION['mensaje_exito']); ?>
                <?php endif; ?>
            </div>

            <!-- FORMULARIO NUEVA CITA -->

            <form class="cita_create_form" id="form_cita" action="../../controllers/users/c_citas_user.php" method="post">

                <h3>Solicitar nueva cita</h3>
                <div class="form_options">
                    <label for="fecha_cita">Fecha *</label>
                    <div class="input_zone">
                        <input type="date"
                            id="fecha_cita"
                            name="fecha_cita"
                            min="<?= $hoy ?>">
                        <small class="input_error"></small>
                    </div>
                </div>

                <div class="form_options">
                    <label for="hora_cita">Hora *</label>
                    <div class="input_zone">
                        <select name="hora_cita" id="hora_cita">
                            <?php
                            for ($h = 8; $h <= 23; $h++):
                                $hora = sprintf('%02d:00', $h);
                            ?>
                                <option value="<?= $hora ?>"><?= $hora ?></option>
                            <?php endfor; ?>
                        </select>

                        <small class="input_error"></small>
                    </div>
                </div>

                <div class="form_options">
                    <label for="motivo_cita">Motivo *</label>
                    <div class="input_zone">
                        <textarea id="motivo_cita" name="motivo_cita"></textarea>
                        <small class="input_error"></small>
                    </div>
                </div>

                <div class="form_buttons">
                    <button class="btn_enviar" type="submit" class="btn_enviar" name="crear_cita">Solicitar cita</button>
                </div>
            </form>


            <section class="section_border_top">
                <!-- LISTADO DE CITAS -->
                <h3>Mis citas</h3>

                <!-- Botón para alternar entre citas actuales y pasadas -->
                <button id="show_past_citas" class="btn_citas">Ver citas pasadas</button>

                <div class="citas_container" id="citas_container">


                    <?php if (!empty($citasActuales)): ?>

                        <?php foreach ($citasActuales as $c): ?>

                            <div class="cita_card">

                                <div class="cita_slider">

                                    <!-- ===== VISTA ===== -->
                                    <div class="cita_view">
                                        <p><strong>Fecha:</strong> <?= (new DateTime($c['fecha_cita']))->format('d/m/Y') ?></p>
                                        <p><strong>Hora:</strong> <?= substr($c['hora_cita'], 0, 5) ?></p>
                                        <p><strong>Motivo:</strong> <?= htmlspecialchars($c['motivo_cita']) ?></p>

                                        <div class="cita_actions">
                                            <button type="button" class="btn_toggle_edit">Editar</button>

                                            <form method="post" action="../../controllers/users/c_citas_user.php">
                                                <input type="hidden" name="idCita" value="<?= $c['idCita'] ?>">
                                                <button type="submit" class="btn_delete" name="borrar_cita">Borrar</button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- ===== EDICIÓN ===== -->
                                    <div class="cita_edit">
                                        <form class="form_modificar_cita" method="post" action="../../controllers/users/c_citas_user.php">
                                            <input type="hidden" name="idCita" value="<?= $c['idCita'] ?>">

                                            <div class="modify_zone">
                                                <input type="date" name="modif_fecha" value="<?= $c['fecha_cita'] ?>" required>
                                                <small class="small_error"></small>
                                            </div>
                                            <div class="modify_zone">
                                                <select name="modif_hora" required>
                                                    <?php for ($h = 8; $h <= 23; $h++): ?>
                                                        <option value="<?= sprintf('%02d:00', $h) ?>"
                                                            <?= substr($c['hora_cita'], 0, 2) == $h ? 'selected' : '' ?>>
                                                            <?= sprintf('%02d:00', $h) ?>
                                                        </option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="modify_zone">
                                                <textarea name="modif_motivo" required><?= htmlspecialchars($c['motivo_cita']) ?></textarea>
                                                <small class="small_error"></small>
                                            </div>

                                            <div class="cita_actions">
                                                <button type="submit" class="btn_enviar_cita" name="modificar_cita">Guardar</button>
                                                <button type="button" class="btn_cancel_edit">Cancelar</button>
                                            </div>

                                        </form>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <p class="no_citas_msg">No hay citas pendientes</p>
                    <?php endif; ?>
                </div>


                <!-- Sección de citas pasadas (inicialmente oculta) -->
                <div class="citas_container" id="past_citas">

                    <?php if (!empty($pastCitas)): ?>

                        <?php foreach ($pastCitas as $c): ?>
                            <div class="cita_card">
                                <div class="cita_slider">
                                    <!-- ===== VISTA ===== -->
                                    <div class="cita_view">
                                        <p><strong>Fecha:</strong> <?= (new DateTime($c['fecha_cita']))->format('d/m/Y') ?></p>
                                        <p><strong>Hora:</strong> <?= substr($c['hora_cita'], 0, 5) ?></p>
                                        <p><strong>Motivo:</strong> <?= htmlspecialchars($c['motivo_cita']) ?></p>
                                        <div class="cita_actions">
                                            <form method="post" action="../../controllers/users/c_citas_user.php">
                                                <input type="hidden" name="idCita" value="<?= $c['idCita'] ?>">
                                                <button type="submit" class="btn_delete" name="borrar_cita">Borrar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p class="no_citas_msg">No hay citas pasadas</p>
                    <?php endif; ?>

                </div>

            </section>
        </main>

        <!-- FOOTER -->
        <footer class="mi_pie">
            <div class="aviso_legal">
                <small>
                    &copy; Club Paddle - Todos los derechos reservados
                </small>
            </div>
        </footer>

    </div>
    <script src="../../assets/scripts/navigationBarListUser.js"></script>
    <script src="../../assets/scripts/v_citas.js"></script>
</body>

</html>