<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/db_conn.php';
require_once __DIR__ . '/../../controllers/db_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $usuarios = obtener_todos_los_usuarios($mysqli_connection);
    $citas    = obtener_citas($mysqli_connection);
} catch (Exception $e) {
    error_log("Error cargando datos citas admin: " . $e->getMessage());
    $usuarios = [];
    $citas = [];
}
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
                        <a class="enlace" href="./usuarios_administracion.php">Usuarios administración</a>
                    </li>
                    <li>
                        <a class="enlace active" href="#">Citas administración</a>
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
        <!-- CUERPO PRINCIPAL-->
        <main class="mi_principal">

            <h2>Administración de Citas</h2>

            <div class="aviso_registro">
                <?php
                if (isset($_SESSION['mensaje_error'])) {
                    echo "<span class='error_message'>{$_SESSION['mensaje_error']}</span>";
                    unset($_SESSION['mensaje_error']);
                }
                if (isset($_SESSION['mensaje_exito'])) {
                    echo "<span class='success_message'>{$_SESSION['mensaje_exito']}</span>";
                    unset($_SESSION['mensaje_exito']);
                }
                ?>
            </div>
            <section class="admin_citas_create">
                <form method="post"
                    action="../../controllers/admin/c_citas_admin.php"
                    class="admin_citas_form">

                    <h3>Crear nueva cita</h3>

                    <div class="form_group">
                        <label>Usuario *</label>
                        <div class="input_zone">
                            <select name="idUser" required>
                                <option value="">-- Seleccionar usuario --</option>
                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?= $u['idUser'] ?>">
                                        <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_group">
                        <label>Fecha *</label>
                        <div class="input_zone">
                            <input type="date"
                                name="fecha_cita"
                                min="<?= date('Y-m-d') ?>"
                                required>
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_group">
                        <label>Hora *</label>
                        <div class="input_zone">
                            <select name="hora_cita" required>
                                <option value="">-- Hora --</option>
                                <?php for ($h = 8; $h <= 23; $h++): ?>
                                    <option value="<?= sprintf('%02d:00:00', $h) ?>">
                                        <?= sprintf('%02d:00', $h) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_group full">
                        <label>Motivo *</label>
                        <div class="input_zone">
                            <textarea name="motivo_cita" required></textarea>
                            <small class="input_error"></small>
                        </div>
                    </div>

                    <div class="form_actions">
                        <button type="submit" name="crear_cita">Crear cita</button>
                    </div>

                </form>
            </section>

            <hr>

            <div class="admin_citas_container">

                <?php foreach ($citas as $c): ?>

                    <div class="cita_card">

                        <div class="admin_cita_view">
                            <p><strong><?= $c['nombre'] . ' ' . $c['apellidos'] ?></strong></p>
                            <p><strong>Fecha: </strong> <?= (new DateTime($c['fecha_cita']))->format('d/m/Y') ?></p>
                            <p><strong>Hora: </strong><?= substr($c['hora_cita'], 0, 5) ?></p>
                            <p><strong>Motivo: </strong><?= htmlspecialchars($c['motivo_cita']) ?></p>
                            <div class="admin_cita_actions">
                                <button type="button" class="btn_toggle_edit">Editar</button>

                                <form method="post" action="../../controllers/admin/c_citas_admin.php">
                                    <input type="hidden" name="idCita" value="<?= $c['idCita'] ?>">
                                    <button class="btn_delete" name="borrar_cita">Borrar</button>
                                </form>
                            </div>
                        </div>

                        <div class="admin_cita_edit hidden">
                            <form method="post" action="../../controllers/admin/c_citas_admin.php">

                                <input type="hidden" name="idCita" value="<?= $c['idCita'] ?>">

                                <input type="date" name="modif_fecha" value="<?= $c['fecha_cita'] ?>" required>

                                <select name="modif_hora" required>
                                    <?php for ($h = 8; $h <= 23; $h++): ?>
                                        <option value="<?= sprintf('%02d:00:00', $h) ?>"
                                            <?= substr($c['hora_cita'], 0, 2) == $h ? 'selected' : '' ?>>
                                            <?= sprintf('%02d:00', $h) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>

                                <textarea name="modif_motivo" required><?= htmlspecialchars($c['motivo_cita']) ?></textarea>

                                <button class="btn_enviar_cita" name="modificar_cita">Guardar</button>
                                <button type="button" name="cancelar_cita" class="btn_cancel_edit">Cancelar</button>
                            </form>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <script src="../../assets/scripts/v_citas_admin.js"></script>


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
</body>

</html>