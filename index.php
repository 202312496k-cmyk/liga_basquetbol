
<?php
require_once __DIR__ . "/configuracion/conexcion.php";

$equipos = 0;
$jugadores = 0;
$partidos = 0;

$resultado = $conn->query("SELECT COUNT(*) AS total FROM equipos");
if ($resultado) {
    $equipos = (int) $resultado->fetch_assoc()["total"];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM jugadores");
if ($resultado) {
    $jugadores = (int) $resultado->fetch_assoc()["total"];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM partidos");
if ($resultado) {
    $partidos = (int) $resultado->fetch_assoc()["total"];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Liga de Básquetbol</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link href="estilos/estilo.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            🏀 Liga de Básquetbol
        </a>

        <div class="navbar-nav">
            <a class="nav-link" href="equipo/equipos.php">Equipos</a>
            <a class="nav-link" href="partido/partidos.php">Partidos</a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <!-- Presentación principal -->
    <div class="p-5 bg-white rounded shadow-sm mb-4 animar-scroll">
        <h1 class="display-5 fw-bold text-primary titulo-liga">
            Sistema de Gestión de Liga
        </h1>

        <p class="lead">
            Administra equipos, jugadores y partidos de básquetbol.
        </p>

        <a href="equipo/equipos.php" class="btn btn-primary">
            Gestionar equipos
        </a>

        <a href="partido/partidos.php" class="btn btn-success">
            Ver partidos
        </a>
    </div>

    <!-- Tarjetas con estadísticas -->
    <div class="row g-4">

        <div class="col-md-4 animar-scroll">
            <div class="card shadow-sm p-4 h-100">
                <h5>Equipos registrados</h5>
                <h2 class="text-primary"><?= $equipos ?></h2>
            </div>
        </div>

        <div class="col-md-4 animar-scroll">
            <div class="card shadow-sm p-4 h-100">
                <h5>Jugadores registrados</h5>
                <h2 class="text-success"><?= $jugadores ?></h2>
            </div>
        </div>

        <div class="col-md-4 animar-scroll">
            <div class="card shadow-sm p-4 h-100">
                <h5>Partidos registrados</h5>
                <h2 class="text-danger"><?= $partidos ?></h2>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/animaciones.js"></script>

</body>
</html>
