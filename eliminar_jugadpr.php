
<?php
require_once __DIR__ . "/configuracion/conexcion.php";
require_once __DIR__ . "/includes/funciones.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirigir("jugadores.php");
}

validar_csrf();

$id = filter_var(
    $_POST["id"] ?? null,
    FILTER_VALIDATE_INT
);

if ($id === false || $id === null || $id <= 0) {
    redirigir("jugadores.php?mensaje=error");
}

$stmt = $conn->prepare(
    "DELETE FROM jugadores WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

redirigir("jugadores.php?mensaje=eliminado");
 