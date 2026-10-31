
<?php
require_once __DIR__ . "/configuracion/conexcion.php";

$equipos = 0;
$jugadores = 0;
$partidos = 0;

// Contar equipos registrados
$resultado = $conn->query("SELECT COUNT(*) AS total FROM equipos");
if ($resultado) {
    $equipos = (int) $resultado->fetch_assoc()["total"];
}

// Contar jugadores registrados
$resultado = $conn->query("SELECT COUNT(*) AS total FROM jugadores");
if ($resultado) {
    $jugadores = (int) $resultado->fetch_assoc()["total"];
}

// Contar partidos registrados
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

    <link href="estilo.css" rel="stylesheet">

    <style>
        /* TARJETAS DE ESTADÍSTICAS */
        .estadistica-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .estadistica-card {
            border: none;
            border-radius: 18px;
            padding: 25px;
            height: 100%;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .estadistica-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.25);
            color: white;
        }

        .card-equipos {
            background: linear-gradient(135deg, #173D65, #102840);
        }

        .card-jugadores {
            background: linear-gradient(135deg, #17634E, #103D32);
        }

        .card-partidos {
            background: linear-gradient(135deg, #80521A, #50300C);
        }

        .estadistica-icono {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .estadistica-card h2 {
            font-size: 42px;
            font-weight: bold;
            margin: 15px 0;
        }

        .estadistica-card h5 {
            font-size: 19px;
            font-weight: 600;
        }

        .estadistica-card p {
            margin-bottom: 0;
            opacity: 0.9;
        }
    </style>
</head>

<body>

<!-- BARRA DE NAVEGACIÓN -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            🏀 Liga de Básquetbol
        </a>

        <div class="navbar-nav">
            <a class="nav-link" href="equipos.php">Equipos</a>
            <a class="nav-link" href="jugadores.php">Jugadores</a>
            <a class="nav-link" href="partidos.php">Partidos</a>
        </div>

    </div>
</nav>

<div class="container py-5">

    <!-- PRESENTACIÓN PRINCIPAL -->
    <div class="p-5 bg-white rounded shadow-sm mb-4 animar-scroll">

        <h1 class="display-5 fw-bold text-primary titulo-liga">
            Sistema de Gestión de Liga
        </h1>

        <p class="lead">
            Administra equipos, jugadores y partidos de básquetbol.
        </p>

        <a href="equipos.php" class="btn btn-primary">
            Gestionar equipos
        </a>

        <a href="partidos.php" class="btn btn-success">
            Ver partidos
        </a>

    </div>

    <!-- ESTADÍSTICAS REGISTRADAS -->
    <h3 class="fw-bold mb-4 animar-scroll">
        📊 Estadísticas generales
    </h3>

    <div class="row g-4">

        <!-- EQUIPOS -->
        <div class="col-md-4 animar-scroll">

            <a href="equipos.php" class="estadistica-link">

                <div class="estadistica-card card-equipos">

                    <div class="estadistica-icono">🏀</div>

                    <h5>Equipos registrados</h5>

                    <h2><?= $equipos ?></h2>

                    <p>Ver todos los equipos →</p>

                </div>

            </a>

        </div>

        <!-- JUGADORES -->
        <div class="col-md-4 animar-scroll">

            <a href="jugador.php" class="estadistica-link">

                <div class="estadistica-card card-jugadores">

                    <div class="estadistica-icono">👥</div>

                    <h5>Jugadores registrados</h5>

                    <h2><?= $jugadores ?></h2>

                    <p>Ver todos los jugadores →</p>

                </div>

            </a>

        </div>

        <!-- PARTIDOS -->
        <div class="col-md-4 animar-scroll">

            <a href="partidos.php" class="estadistica-link">

                <div class="estadistica-card card-partidos">

                    <div class="estadistica-icono">🏆</div>

                    <h5>Partidos registrados</h5>

                    <h2><?= $partidos ?></h2>

                    <p>Ver todos los partidos →</p>

                </div>

            </a>

        </div>

    </div>

</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="animaciones.js"></script>

</body>
</html>
