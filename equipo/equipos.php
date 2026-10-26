<?php
require_once "configuracion/conexcion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $entrenador = trim($_POST["entrenador"] ?? "");

    if ($nombre !== "") {
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

        header("Location: equipos.php?mensaje=creado");
        exit;
    }
}

if (isset($_GET["eliminar"])) {
    $id = (int)$_GET["eliminar"];

    $stmt = $conn->prepare("DELETE FROM equipos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: equipos.php?mensaje=eliminado");
    exit;
}

$resultado = $conn->query(
    "SELECT e.id, e.nombre, e.ciudad, e.entrenador,
            COUNT(j.id) AS total_jugadores
     FROM equipos e
     LEFT JOIN jugadores j ON j.equipo_id = e.id
     GROUP BY e.id, e.nombre, e.ciudad, e.entrenador
     ORDER BY e.id DESC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1>Gestión de equipos</h1>
    <a href="index.html" class="btn btn-secondary mb-3">Inicio</a>

    <?php if (isset($_GET["mensaje"])): ?>
        <div class="alert alert-success">
            Operación realizada correctamente.
        </div>
    <?php endif; ?>

    <div class="card p-4 mb-4">
        <h4>Registrar equipo</h4>

        <form method="POST">
            <input class="form-control mb-3"
                   name="nombre" placeholder="Nombre del equipo"
                   maxlength="100" required>

            <input class="form-control mb-3"
                   name="ciudad" placeholder="Ciudad"
                   maxlength="100">

            <input class="form-control mb-3"
                   name="entrenador" placeholder="Entrenador"
                   maxlength="100">

            <button class="btn btn-primary">
                Guardar equipo
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Equipo</th>
                    <th>Ciudad</th>
                    <th>Entrenador</th>
                    <th>Jugadores</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($equipo = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$equipo["id"] ?></td>
                    <td><?= htmlspecialchars($equipo["nombre"]) ?></td>
                    <td><?= htmlspecialchars($equipo["ciudad"] ?? "") ?></td>
                    <td><?= htmlspecialchars($equipo["entrenador"] ?? "") ?></td>
                    <td><?= (int)$equipo["total_jugadores"] ?></td>
                    <td>
                        <a class="btn btn-danger btn-sm"
                           href="equipos.php?eliminar=<?= (int)$equipo["id"] ?>"
                           onclick="return confirm('¿Eliminar este equipo y sus registros relacionados?')">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
