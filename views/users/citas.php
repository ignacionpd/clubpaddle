<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/users/c_citas_data.php';

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
$citas = obtener_citas_usuario($_SESSION['user_data']['id_user']);
$hoy = date('Y-m-d');

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
        <!-- HEADER -->
        <header class="mi_encabezado">
            <h1>Club Paddle</h1>
            <nav class="navigationBar">
                <ul class="navigationBarList">
                    <li><a class="enlace" href="../../index.php">Inicio</a></li>
                    <li><a class="enlace" href="../noticias.php">Noticias</a></li>
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

            <form class="cita_create_form" id="form_cita" action="../../controllers/users/c_citas_create.php" method="post">

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

            <hr>

            <!-- LISTADO DE CITAS -->
            <h3>Mis citas</h3>

            <?php if (empty($citas)): ?>
                <p>No tienes citas registradas.</p>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <div class="cita_card">

                        <!-- ===== VISTA SOLO LECTURA ===== -->
                        <div class="cita_view">
                            <p><strong>Fecha:</strong>
                                <?= date('d-m-Y', strtotime($cita['fecha_cita'])) ?>
                            </p>
                            <p><strong>Hora:</strong>
                                <?= isset($cita['hora_cita']) ? substr($cita['hora_cita'], 0, 5) : '—' ?>
                            </p>
                            <p><strong>Motivo:</strong>
                                <?= htmlspecialchars($cita['motivo_cita']) ?></p>
                            <div class="form_buttons">
                                <button type="button" class="btn_toggle_edit"> Modificar cita </button>
                                <form class="form_delete_cita" action="../../controllers/users/c_citas_delete.php" method="post">
                                    <input type="hidden" name="idCita" value="<?= $cita['idCita'] ?>">
                                    <button type="submit" name="borrar_cita" class="btn_delete"> Borrar cita </button>
                                </form>
                            </div>
                        </div>

                        <!-- ===== FORMULARIO OCULTO DE EDICIÓN ===== -->
                        <div class="cita_edit"> 
                            <div class="cita_edit_info">
                                <p><strong>Editando cita:</strong></p>
                                <p> <?= date('d-m-Y', strtotime($cita['fecha_cita'])) ?></p>
                                <p> <?= isset($cita['hora_cita']) ? substr($cita['hora_cita'], 0, 5) : '—' ?></p>
                                <p> <?= htmlspecialchars($cita['motivo_cita']) ?></p>
                            </div>
                            <form class="form_editar_cita" action="../../controllers/users/c_citas_update.php" method="post">

                                <input type="hidden" name="idCita" value="<?= $cita['idCita'] ?>">
                                <div class="form_options">
                                    <label>Fecha</label>
                                    <div class="input_zone"> <input type="date" name="fecha_cita" value="<?= $cita['fecha_cita'] ?>" min="<?= $hoy ?>">
                                        <small class="input_error"></small>
                                    </div>
                                </div>

                                <div class="form_options">
                                    <label>Hora</label>
                                    <div class="input_zone">
                                        <select name="hora_cita"> <?php for ($h = 8; $h <= 23; $h++): $hora = sprintf('%02d:00', $h); ?>
                                                <option value="<?= $hora ?>" <?= ($cita['hora_cita'] ?? '') === $hora ? 'selected' : '' ?>> <?= $hora ?> </option> <?php endfor; ?>
                                        </select>
                                        <small class="input_error"></small>
                                    </div>
                                </div>

                                <div class="form_options">
                                    <label>Motivo</label>
                                    <div class="input_zone">
                                        <textarea name="motivo_cita"><?= htmlspecialchars($cita['motivo_cita']) ?></textarea>
                                        <small class="input_error"></small>
                                    </div>
                                </div>

                                <div class="form_buttons">
                                    <button type="submit" class="btn_editar_cita" name="editar_cita">Guardar cambios</button>
                                    <button type="button" class="btn_cancel_edit">Cancelar</button>
                                </div>

                            </form>
                        </div>
                </div> 
                 <?php endforeach; ?> <?php endif; ?>   
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

    <script src="../../assets/scripts/v_citas.js"></script>
</body>

</html>