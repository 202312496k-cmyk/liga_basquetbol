
<?php
require_once __DIR__ . "/configuracion/conexcion.php";
require_once __DIR__ . "/includes/funciones.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirigir("jugadores.php");
}

validar_csrf();

$id = (int)($_POST["id"] ?? 0);
$equipo_id = (int)($_POST["equipo_id"] ?? 0);
$nombres = trim($_POST["nombres"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$dorsal = filter_var($_POST["dorsal"] ?? null, FILTER_VALIDATE_INT);
$edad = filter_var($_POST["edad"] ?? null, FILTER_VALIDATE_INT);
$posicion = $_POST["posicion"] ?? "";

$posiciones = ["Base", "Escolta", "Alero", "Ala-pívot", "Pívot"];

if (
    $equipo_id <= 0 ||
    $nombres === "" ||
    $apellidos === "" ||
    $dorsal === false || $dorsal === null ||
    $dorsal < 0 || $dorsal > 99 ||
    $edad === false || $edad === null ||
    $edad < 1 || $edad > 100 ||
    !in_array($posicion, $posiciones, true)
) {
    redirigir("jugadores.php?mensaje=error");
}

try {
    if ($id > 0) {
        $stmt = $conn->prepare(
            "UPDATE jugadores
             SET equipo_id = ?, nombres = ?, apellidos = ?,
                 dorsal = ?, posicion = ?, edad = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "issisii",
            $equipo_id,
            $nombres,
            $apellidos,
            $dorsal,
            $posicion,
            $edad,
            $id
        );

        $stmt->execute();
        $stmt->close();

        redirigir("jugadores.php?mensaje=actualizado");
    }

    $stmt = $conn->prepare(
        "INSERT INTO jugadores
         (equipo_id, nombres, apellidos, dorsal, posicion, edad)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issisi",
        $equipo_id,
        $nombres,
        $apellidos,
        $dorsal,
        $posicion,
        $edad
    );

    $stmt->execute();
    $stmt->close();

    redirigir("jugadores.php?mensaje=creado");

} catch (mysqli_sql_exception $e) {
    if ((int)$e->getCode() === 1062) {
        redirigir("jugadores.php?mensaje=duplicado");
    }

    error_log($e->getMessage());
    http_response_code(500);
    exit("Error al guardar el jugador. Revisa el registro de errores de PHP.");
}
