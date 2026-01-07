<?php
require_once __DIR__ . '/../config/config.php';
require_once '../controllers/c_noticias.php';


# Comprobar si existe una sesión activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
                        <a class="enlace active" href="#">Noticias</a>
                    </li>
                    <?php
                    if (isset($_SESSION["user_data"])) {
                        if ($_SESSION["user_data"]['rol'] == "admin") {
                            echo '<li>
                                       <a class="enlace" href="./admin/usuarios_administracion.php">Usuarios administración</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./admin/citas_administracion.php">Citas administración</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./admin/noticias_administracion.php">Noticias administración</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./admin/admin_profile.php">Perfil</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="../controllers/logout.php">Cerrar sesión</a>
                                  </li>';
                        } elseif ($_SESSION["user_data"]['rol'] == "user") {
                            echo '<li>
                                       <a class="enlace" href="./users/citas.php">Citas</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./users/user_profile.php">Perfil</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="../controllers/logout.php">Cerrar sesión</a>
                                  </li>';
                        }
                    } else {
                        if (!isset($_SESSION[''])) {
                            echo '<li>
                                        <a class="enlace" href="./registro.php">Registro</a>
                                  </li>
                                  <li>
                                        <a class="enlace" href="./login.php">Login</a>
                                  </li>';
                        }
                    }
                    ?>
                </ul>
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

</body>

</html>