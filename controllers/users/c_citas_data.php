<?php
require_once __DIR__ . '/../db_conn.php';

/**
 * Obtener todas las citas de un usuario
 *
 * @param int $idUser
 * @return array
 */
function obtener_citas_usuario(int $idUser): array {

    $citas = [];
    $stmt = null;

    try {
        $mysqli = connectToDatabase();

        if (!$mysqli) {
            throw new Exception("Error de conexión a la base de datos.");
        }

        $sql = "
            SELECT idCita, fecha_cita, hora_cita, motivo_cita
            FROM citas
            WHERE idUser = ?
            ORDER BY fecha_cita ASC, hora_cita ASC
        ";


        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta.");
        }

        $stmt->bind_param("i", $idUser);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta.");
        }

        $result = $stmt->get_result();

        $citas = $result->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {

        // Registrar el error en el log
        error_log("Error al obtener citas del usuario {$idUser}: " . $e->getMessage());

        // En caso de error devolvemos array vacío para no romper la vista
        $citas = [];

    } finally {

        if ($stmt !== null) {
            $stmt->close();
        }
    }

    return $citas;
}