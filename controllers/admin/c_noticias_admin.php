<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../db_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===============================
   SEGURIDAD
=============================== */

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['rol'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

/* ===============================
   CREAR NOTICIA
=============================== */

if (isset($_POST['crear_noticia'])) {

    try {
        $titulo = trim($_POST['titulo'] ?? '');
        $texto  = trim($_POST['texto'] ?? '');
        $fecha  = date('Y-m-d');
        $idUser = $_SESSION['user_data']['id_user'];

        if ($titulo === '' || $texto === '') {
            throw new Exception("El título y el texto son obligatorios.");
        }

        if (empty($_FILES['imagen']['name'])) {
            throw new Exception("La imagen es obligatoria.");
        }

        $nombreImagen = uniqid() . '_' . $_FILES['imagen']['name'];
        $rutaDestino  = __DIR__ . '/../../uploads/noticias/' . $nombreImagen;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            throw new Exception("Error al subir la imagen.");
        }

        crear_noticia(
            $titulo,
            $nombreImagen,
            $texto,
            $fecha,
            $idUser,
            $mysqli_connection
        );

        $_SESSION['mensaje_exito'] = "Noticia creada correctamente.";

    } catch (Exception $e) {
        error_log("Crear noticia: " . $e->getMessage());
        $_SESSION['mensaje_error'] = $e->getMessage();
    }

    header("Location: ../../views/admin/noticias_administracion.php");
    exit;
}

/* ===============================
   MODIFICAR NOTICIA
=============================== */

if (isset($_POST['modificar_noticia'])) {

    try {
        $idNoticia = (int) ($_POST['idNoticia'] ?? 0);
        $titulo = trim($_POST['modif_titulo'] ?? '');
        $texto  = trim($_POST['modif_texto'] ?? '');
        $fecha     = date('Y-m-d');

        if ($idNoticia <= 0 || $titulo === '' || $texto === '') {
            throw new Exception("Datos inválidos para modificar la noticia.");
        }

        $imagen = null;

        // Imagen opcional
        if (!empty($_FILES['imagen']['name'])) {
            $imagen = uniqid() . '_' . $_FILES['imagen']['name'];
            $rutaDestino = __DIR__ . '/../../uploads/noticias/' . $imagen;

            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                throw new Exception("Error al subir la nueva imagen.");
            }
        }

        actualizar_noticia(
            $idNoticia,
            $titulo,
            $imagen,
            $texto,
            $fecha,
            $mysqli_connection
        );

        $_SESSION['mensaje_exito'] = "Noticia modificada correctamente.";

    } catch (Exception $e) {
        error_log("Modificar noticia: " . $e->getMessage());
        $_SESSION['mensaje_error'] = $e->getMessage();
    }

    header("Location: ../../views/admin/noticias_administracion.php");
    exit;
}

/* ===============================
   BORRAR NOTICIA
=============================== */

if (isset($_POST['borrar_noticia'])) {

    try {
        $idNoticia = (int) ($_POST['idNoticia'] ?? 0);

        if ($idNoticia <= 0) {
            throw new Exception("ID de noticia inválido.");
        }

        borrar_noticia($idNoticia, $mysqli_connection);

        $_SESSION['mensaje_exito'] = "Noticia eliminada correctamente.";

    } catch (Exception $e) {
        error_log("Borrar noticia: " . $e->getMessage());
        $_SESSION['mensaje_error'] = "Error al borrar la noticia.";
    }

    header("Location: ../../views/admin/noticias_administracion.php");
    exit;
}
