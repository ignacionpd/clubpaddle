<?php
    require_once __DIR__ . '/config/config.php';

    # Comprobar si existe una sesión activa y en caso de que no así la crearemos
    if(session_status() == PHP_SESSION_NONE){
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
    <link rel="stylesheet" href="./assets/css/estilos.css">
    <!-- FAVICON -->
    <link rel="icon" href="./assets/css/images/favicon.png" type="image/png">
</head>
<body>
    <div class="mi_contenedor">
        <!-- HEADER -->
        <header class="mi_encabezado">
            <h1>Club Paddle</h1>
            <nav class="navigationBar">
                <ul class="navigationBarList">
                    <li>
                        <a class="enlace active" href="#">Inicio</a>
                    </li>
                    <li>
                        <a class="enlace" href="./views/noticias.php">Noticias</a>
                    </li>       
                    <?php
                    if(isset($_SESSION["user_data"])){
                        if($_SESSION["user_data"]['rol'] == "admin"){
                            echo '<li>
                                       <a class="enlace" href="./views/admin/usuarios_administracion.php">Usuarios administración</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./views/admin/citas_administracion.php">Citas administración</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./views/admin/noticias_administracion.php">Noticias administración</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./views/admin/admin_profile.php">Perfil</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./controllers/users/logout.php">Cerrar sesión</a>
                                  </li>';
                        }elseif($_SESSION["user_data"]['rol'] == "user"){
                            echo '<li>
                                       <a class="enlace" href="./views/users/citas.php">Citas</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./views/users/user_profile.php">Perfil</a>
                                  </li>
                                  <li>
                                       <a class="enlace" href="./controllers/users/logout.php">Cerrar sesión</a>
                                  </li>';
                        }
                        
                    }else{
                            if(!isset($_SESSION[''])){
                            echo '<li>
                                        <a class="enlace" href="./views/registro.php">Registro</a>
                                  </li>
                                  <li>
                                        <a class="enlace" href="./views/login.php">Login</a>
                                  </li>';
                            }
                    }
                    ?>    
                </ul>
            </nav>
        </header>
        <!-- CUERPO PRINCIPAL-->
        <main class="mi_principal">
            <!--<div class="aviso_conn">
                AVISO: 
                <?php
                    require_once  'controllers/db_conn.php';
                ?>
            </div>  -->
            
            <section class="presentacion">
                <h2>¿Quiénes somos?</h2>
                <p>En <b>Club Paddle</b> vivimos el pádel como una experiencia completa. Ponemos a disposición de nuestros socios y visitantes <b>pistas de pádel modernas, cuidadas y totalmente equipadas</b>, pensadas tanto para jugadores amateurs como para quienes buscan un nivel más competitivo.</p>

                <p>Contamos con <b>amplia disponibilidad horaria</b>, un sistema de reservas ágil y flexible, y un entorno cómodo y seguro para que solo tengas que preocuparte de disfrutar del juego. Nuestras instalaciones están preparadas para entrenamientos, partidos amistosos y competiciones oficiales.</p>

                <p>Además, organizamos <b>torneos, ligas y eventos deportivos</b>, fomentando un ambiente dinámico y participativo entre jugadores de todos los niveles. También ofrecemos la posibilidad de celebrar <b>festejos, encuentros privados y eventos corporativos</b>, adaptándonos a las necesidades de cada grupo.</p>

                <p>Nuestro objetivo es claro: <b>ofrecer un servicio profesional, cercano y de calidad</b>, promoviendo el deporte, la diversión y el espíritu de comunidad.
                Te invitamos a formar parte de nuestro club y a disfrutar del pádel como se merece.</p>
            </section>
            
            <section class="galeria-container">
                <h2>Nuestras instalaciones</h2>
                <div class="galeria">
                    <img src="./assets/images/1.jpg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/2.jpeg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/3.jpg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/4.jpg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/5.jpg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/6.jpg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/7.jpg" alt="club paddle"  width="904" height="400">
                    <img src="./assets/images/8.png" alt="club paddle"  width="904" height="400">
                </div>
            </section>

            <section class="enlaces-container">
                <h2>Páginas relacionadas</h2>
                <div class="enlaces">
                    <a href="https://padelworldpress.es/" target="_blank"
                     rel="noopener noreferrer"><img src="./assets/images/enlaces/Padel-Word-Press.png" alt="Paddle World Press" width="300" height="100"></a>
                    <a href="https://www.padeladdict.com/" target="_blank"
                    rel="noopener noreferrer"><img src="./assets/images/enlaces/paddle-addict.png" alt="Paddle Addict" width="500" height="500"></a>
                    <a href="https://www.padelnuestro.com/" target="_blank"
                    rel="noopener noreferrer"><img src="./assets/images/enlaces/PADEL-NUESTRO.png" alt="Paddle Nuestro" width="715" height="360"></a>
                    <a href="https://www.zonadepadel.es/" target="_blank"
                    rel="noopener noreferrer"><img src="./assets/images/enlaces/zona-de-padel.jpg" alt="Zona Paddle" width="348" height="350"></a>
                    <a href="https://www.padelspain.net/" target="_blank"
                     rel="noopener noreferrer"><img src="./assets/images/enlaces/Padel_spain.png" alt="Paddle Spain" width="274" height="162"></a>
                    <a href="https://www.padelfip.com/es/" target="_blank"
                     rel="noopener noreferrer"><img src="./assets/images/enlaces/fip.png" alt="Federación Internacional de Paddle" width="358" height="141"></a>
                    
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
</body>
</html>