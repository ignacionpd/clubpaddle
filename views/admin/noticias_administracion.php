<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/db_conn.php';
require_once __DIR__ . '/../../controllers/db_functions.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verificamos si no hay usuario logueado
if (!isset($_SESSION['user_data'])) {
    header('Location: ../../index.php');
    exit;
}

// Comprobamos si el Usuario no es admin
if ($_SESSION['user_data']['rol'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

// CARGA DE NOTICIAS

try {
    $noticias = obtener_noticias($mysqli_connection);
} catch (Exception $e) {
    error_log("Error cargando noticias: " . $e->getMessage());
    $noticias = [];
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
    <link rel="icon" href="../../assets/css/images/favicon.png" type="image/png">
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
                        <a class="enlace" href="./citas_administracion.php">Citas administración</a>
                    </li>
                    <li>
                        <a class="enlace active" href="#">Noticias administración</a>
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

            <h2>Administración de Noticias</h2>
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
            <div class="admin_noticia_create">

                <h3>Crear nueva noticia</h3>

                <form id="form_crear_noticia"
                    method="post"
                    action="../../controllers/admin/c_noticias_admin.php"
                    enctype="multipart/form-data">

                    <!-- TÍTULO -->
                    <div class="form_row">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required>
                        <small class="input_error"></small>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="form_row">
                        <label for="texto">Contenido *</label>
                        <textarea id="texto" name="texto" required></textarea>
                        <small class="input_error"></small>
                    </div>

                    <!-- IMAGEN -->
                    <div class="form_row">
                        <label for="imagen">Imagen *</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" required>
                        <small class="input_error"></small>

                        <!-- PREVIEW -->
                        <img id="preview_imagen" class="preview hidden" alt="Vista previa">
                    </div>

                    <!-- ACCIONES -->
                    <div class="form_actions">
                        <button type="submit" name="crear_noticia">
                            Crear noticia
                        </button>
                    </div>

                </form>

            </div>




            <div id="admin_noticias_container" class="admin_noticias_container">
                <?php if (!empty($noticias)): ?>
                    <?php foreach ($noticias as $n): ?>

                        <div class="admin_noticia_card">

                            <!-- ===== VISTA ===== -->
                            <div class="admin_noticia_view">
                                <h3><?= htmlspecialchars($n['titulo']) ?></h3>

                                <img
                                    src="../../uploads/noticias/<?= htmlspecialchars($n['imagen']) ?>"
                                    alt="<?= htmlspecialchars($n['titulo']) ?>">

                                <p><?= nl2br(htmlspecialchars($n['texto'])) ?></p>

                                <small><?= (new DateTime($n['fecha']))->format('d/m/Y') ?> · <?= htmlspecialchars($n['nombre']) ?></small>
                        
                                <div class="admin_noticia_actions">
                                    <button type="button" class="btn_edit">Editar</button>

                                    <form method="post" action="../../controllers/admin/c_noticias_admin.php">
                                        <input type="hidden" name="idNoticia" value="<?= $n['idNoticia'] ?>">
                                        <button type="submit" class="btn_delete" name="borrar_noticia">
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- ===== EDICIÓN ===== -->
                            <div class="admin_noticia_edit hidden">
                                <form method="post"
                                    action="../../controllers/admin/c_noticias_admin.php"
                                    enctype="multipart/form-data">

                                    <input type="hidden" name="idNoticia" value="<?= $n['idNoticia'] ?>">

                                    <input type="text" name="modif_titulo" value="<?= htmlspecialchars($n['titulo']) ?>" required>

                                    <textarea name="modif_texto" required><?= htmlspecialchars($n['texto']) ?></textarea>

                                    <input type="file" name="imagen" accept="image/*">
                                    <div>
                                        <button type="submit" class="btn_modificar_noticia" name="modificar_noticia">Guardar</button>
                                        <button type="button" class="btn_cancel_edit">Cancelar</button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay noticias registradas.</p>
                <?php endif; ?>
            </div>
            <script src="../../assets/scripts/v_noticias_admin.js"></script>


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