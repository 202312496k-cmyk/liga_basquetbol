
<?php
require_once __DIR__ . "../configuracion/conexcion.php";
require_once __DIR__ . "../funciones.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirigir("equipos.php");
}

validar_csrf();

$id = (int)($_POST["id"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$ciudad = trim($_POST["ciudad"] ?? "");
$entrenador = trim($_POST["entrenador"] ?? "");

if ($nombre === "") {
    redirigir("equipos.php?mensaje=error");
}

try {
    if ($id > 0) {
        $stmt = $conn->prepare(
            "UPDATE equipos
             SET nombre = ?, ciudad = ?, entrenador = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssi",
            $nombre,
            $ciudad,
            $entrenador,
            $id
        );

        $stmt->execute();

        redirigir("equipos.php?mensaje=actualizado");
    }

    $stmt = $conn->prepare(
        "INSERT INTO equipos (nombre, ciudad, entrenador)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "sss",
        $nombre,
        $ciudad,
        $entrenador
    );

    $stmt->execute();

    redirigir("equipos.php?mensaje=creado");

} catch (mysqli_sql_exception $e) {
    if ((int)$e->getCode() === 1062) {
        redirigir("equipos.php?mensaje=duplicado");
    }

    throw $e;
}
