<?php
require_once __DIR__ . '/../config/config.php';
require_once '../controllers/c_noticias.php';


# Comprobar si existe una sesión activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Paddle</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="../assets/images/favicon/site.webmanifest">
</head>

<body>
    <div class="mi_contenedor">
        <!-- HEADER -->
        <header class="mi_encabezado">
            <h1>Club Paddle</h1>

            <nav class="navigationBar">

                <ul class="navigationBarList">
                    <?php if (isset($_SESSION["user_data"])): ?>
                        <label class="label_check" id="menuToggle">
                            <span class="icon-menu"></span>
                        </label>
                    <?php endif; ?>
                    <li><a class="enlace" href="../index.php">Inicio</a></li>
                    <li><a class="enlace active" href="#">Noticias</a></li>

                    <?php if (!isset($_SESSION["user_data"])): ?>
                        <li><a class="enlace" href="./registro.php">Registro</a></li>
                        <li><a class="enlace" href="./login.php">Login</a></li>
                    <?php endif; ?>
                </ul>

                <!-- MENÚ DESPLEGABLE SOLO SI HAY SESIÓN -->
                <?php if (isset($_SESSION["user_data"])): ?>
                    <ul class="navigationBarListUser">

                        <?php if ($_SESSION["user_data"]['rol'] === "admin"): ?>

                            <li><a class="enlace" href="./admin/usuarios_administracion.php">Administración Usuarios</a></li>
                            <li><a class="enlace" href="./admin/citas_administracion.php">Administración Citas</a></li>
                            <li><a class="enlace" href="./admin/noticias_administracion.php">Administración Noticias</a></li>
                            <li><a class="enlace" href="./admin/admin_profile.php">Perfil</a></li>
                            <li><a class="enlace" href="../controllers/users/logout.php">Cerrar sesión</a></li>

                        <?php elseif ($_SESSION["user_data"]['rol'] === "user"): ?>

                            <li><a class="enlace" href="./users/citas.php">Citas</a></li>
                            <li><a class="enlace" href="./users/user_profile.php">Perfil</a></li>
                            <li><a class="enlace" href="../controllers/logout.php">Cerrar sesión</a></li>

                        <?php endif; ?>

                    </ul>
                <?php endif; ?>

            </nav>
        </header>
        <!-- CUERPO PRINCIPAL-->
        <main class="mi_principal">
            <h2>Noticias</h2>
            <?php if (empty($noticias)): ?>
                <p>No hay noticias publicadas.</p>
            <?php else: ?>

                <?php foreach ($noticias as $noticia): ?>
                    <article class="noticia">

                        <h3>
                            <?= htmlspecialchars($noticia['titulo']) ?>
                        </h3>

                        <div class="noticia-cuerpo">

                            <div class="noticia-media">
                                    <img
                                    src="../uploads/noticias/<?= htmlspecialchars($noticia['imagen']) ?>"
                                    alt="Imagen de la noticia">
                            </div>

                            <div class="noticia-texto">
                                <p class="meta">
                                    Publicado el <?= date('d/m/Y', strtotime($noticia['fecha'])) ?><br>
                                    por <strong><?= htmlspecialchars($noticia['autor']) ?></strong>
                                </p>

                                <p><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>
                            </div>

                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

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

    <script src="../assets/scripts/navigationBarListUser.js"></script>
</body>

</html>