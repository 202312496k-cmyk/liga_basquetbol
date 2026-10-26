
<?php
require_once "configuracion/conexcion.php";

$equipos = $conn->query(
    "SELECT id, nombre FROM equipos ORDER BY nombre"
);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $local = (int)($_POST["equipo_local"] ?? 0);
    $visitante = (int)($_POST["equipo_visitante"] ?? 0);
    $fecha = str_replace("T", " ", $_POST["fecha"] ?? "");
    $lugar = trim($_POST["lugar"] ?? "");

    if ($local === $visitante || $local === 0 || $visitante === 0) {
        $error = "Selecciona dos equipos diferentes.";
    } elseif (strlen($fecha) < 16) {
        $error = "Ingresa una fecha válida.";
    } else {
        if (strlen($fecha) === 16) {
            $fecha .= ":00";
        }

        $stmt = $conn->prepare(
            "INSERT INTO partidos
             (equipo_local, equipo_visitante, fecha, lugar)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iiss",
            $local,
            $visitante,
            $fecha,
            $lugar
        );

        $stmt->execute();

        header("Location: partidos.php?mensaje=creado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Programar partido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card p-4 shadow-sm">
        <h2>Programar partido</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($equipos->num_rows < 2): ?>
            <p>Debes registrar al menos dos equipos.</p>
            <a href="equipos.php" class="btn btn-primary">Registrar equipos</a>
        <?php else: ?>
            <form method="POST">
                <label class="form-label">Equipo local</label>
                <select class="form-select mb-3" name="equipo_local" required>
                    <?php
                    $equipos->data_seek(0);
                    while ($e = $equipos->fetch_assoc()):
                    ?>
                        <option value="<?= (int)$e["id"] ?>">
                            <?= htmlspecialchars($e["nombre"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label class="form-label">Equipo visitante</label>
                <select class="form-select mb-3" name="equipo_visitante" required>
                    <?php
                    $equipos->data_seek(0);
                    while ($e = $equipos->fetch_assoc()):
                    ?>
                        <option value="<?= (int)$e["id"] ?>">
                            <?= htmlspecialchars($e["nombre"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label class="form-label">Fecha y hora</label>
                <input type="datetime-local" name="fecha"
                       class="form-control mb-3" required>

                <label class="form-label">Lugar</label>
                <input name="lugar" class="form-control mb-3"
                       maxlength="150">

                <button class="btn btn-success">Guardar partido</button>
                <a href="partidos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
