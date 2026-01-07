<?php
require_once __DIR__ . '/../config/config.php';
require_once 'db_conn.php';

$sql = "
    SELECT 
        n.idNoticia,
        n.titulo,
        n.texto,
        n.imagen,
        n.fecha,
        u.nombre AS autor
    FROM noticias n
    INNER JOIN users_data u ON n.idUser = u.idUser
    ORDER BY n.fecha DESC
";

$result = $mysqli_connection->query($sql);

if (!$result) {
    die("Error en la consulta: " . $mysqli_connection->error);
}

$noticias = [];

while ($row = $result->fetch_assoc()) {
    $noticias[] = $row;
}

$result->free();