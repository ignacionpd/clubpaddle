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
<html lang="es">
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
                        <a class="enlace active" href="#">Registro</a>
                    </li>
                    <li>
                        <a class="enlace" href="./login.php">Login</a>
                    </li>
                </ul>
            </nav>
        </header>
        <!-- CUERPO PRINCIPAL-->
        <main class="mi_principal">
            <section>
                <h2>Formulario registro</h2>
                <div class="aviso_registro">
                    <?php
                        # Comprobar si hay mensajes de error
                        if(isset($_SESSION["mensaje_error"])){
                            echo "<span class='error_message'>" . $_SESSION['mensaje_error'] . "</span>";

                            # Eliminar el mensaje de error
                            unset($_SESSION["mensaje_error"]);
                        }
                        

                        # Comprobar si hay mensajes de exito
                        if(isset($_SESSION["mensaje_exito"])){
                            echo "<span class='success_message'>" . $_SESSION['mensaje_exito'] . "</span>";

                            # Eliminar el mensaje de error
                            unset($_SESSION["mensaje_exito"]);
                        }
                        
                    ?>
                </div>
                
                    <form class="register_login_form" id="register_form" action="../controllers/c_registro.php" method="post">
                        
                            <div class="form_options">
                                <label for="user_name">Nombre: *</label>
                                <div class="input_zone">
                                    <input type="text" id="user_name" name="user_name" placeholder="Escriba su nombre..." title="El nombre deberá contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto">
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
                                    <input type="text" id="user_email" name="user_email" placeholder="Escriba su correo electrónico..." title="El correo electrónico similar a: xxxxx@xxx.xxx">
                                    <small class="input_error"></small>
                                </div>
                            </div>
                            <div class="form_options">
                                <label for="user_tel">Teléfono: *</label>
                                <div class="input_zone">
                                    <input type="text" id="user_tel" name="user_tel" placeholder="Escriba su número de teléfono..." title="El telefono deberá contener 9 dígitos">
                                    <small class="input_error"></small>
                                </div>
                            </div>
                                                    
                            <div class="form_options">
                                <label for="user_date">Fecha de nacimiento: *</label>
                                <div class="input_zone">
                                    <input type="date" id="user_date" name="user_date" title="Seleccione su fecha de nacimiento">                                  
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
                                <a href="./login.php">Ya estoy registrado</a>
                            </div>  
                            <div class="form_buttons">
                                <input type="reset" class="btn_reset" value="Borrar">
                                <input type="submit" class="btn_enviar" name="registrarse" value="Enviar">
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

    <script src="../assets/scripts/v_register.js"></script>
    <script src="../assets/scripts/show_password.js"></script>
</body>
</html>