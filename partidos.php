
<?php
require_once "configuracion/conexcion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);

    if (isset($_POST["eliminar"])) {
        $stmt = $conn->prepare("DELETE FROM partidos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: partidos.php?mensaje=eliminado");
        exit;
    }

    $local = filter_input(INPUT_POST, "puntos_local", FILTER_VALIDATE_INT);
    $visitante = filter_input(INPUT_POST, "puntos_visitante", FILTER_VALIDATE_INT);

    if ($local !== false && $local !== null &&
        $visitante !== false && $visitante !== null &&
        $local >= 0 && $visitante >= 0) {

        $stmt = $conn->prepare(
            "UPDATE partidos
             SET puntos_local = ?, puntos_visitante = ?, estado = 'Finalizado'
             WHERE id = ?"
        );

        $stmt->bind_param("iii", $local, $visitante, $id);
        $stmt->execute();
    }

    header("Location: partidos.php?mensaje=actualizado");
    exit;
}

$sql = "SELECT p.*, el.nombre AS local_nombre,
               ev.nombre AS visitante_nombre
        FROM partidos p
        INNER JOIN equipos el ON el.id = p.equipo_local
        INNER JOIN equipos ev ON ev.id = p.equipo_visitante
        ORDER BY p.fecha DESC";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Partidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1>Control de partidos</h1>

    <a href="index.html" class="btn btn-secondary mb-3">Inicio</a>
    <a href="crear_partido.php" class="btn btn-success mb-3">+ Programar partido</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered bg-white align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Local</th>
                    <th>Marcador</th>
                    <th>Visitante</th>
                    <th>Estado</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$resultado->num_rows): ?>
                <tr><td colspan="6" class="text-center">No hay partidos.</td></tr>
            <?php endif; ?>

            <?php while ($p = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($p["fecha"]) ?></td>
                    <td><?= htmlspecialchars($p["local_nombre"]) ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-1">
                            <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
                            <input type="number" min="0" name="puntos_local"
                                   class="form-control" style="width:80px"
                                   value="<?= $p["puntos_local"] === null ? "" : (int)$p["puntos_local"] ?>"
                                   required>
                            <span>-</span>
                            <input type="number" min="0" name="puntos_visitante"
                                   class="form-control" style="width:80px"
                                   value="<?= $p["puntos_visitante"] === null ? "" : (int)$p["puntos_visitante"] ?>"
                                   required>
                            <button class="btn btn-primary btn-sm">Guardar</button>
                        </form>
                    </td>
                    <td><?= htmlspecialchars($p["visitante_nombre"]) ?></td>
                    <td><?= htmlspecialchars($p["estado"]) ?></td>
                    <td>
                        <form method="POST"
                              onsubmit="return confirm('¿Eliminar este partido?')">
                            <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
                            <button name="eliminar" value="1"
                                    class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
