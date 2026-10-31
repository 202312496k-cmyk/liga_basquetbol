
<?php
require_once __DIR__ . "/configuracion/conexcion.php";

$mensaje = "";
$editar = null;

// REGISTRAR Y ACTUALIZAR JUGADOR
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = (int)($_POST["id"] ?? 0);
    $nombres = trim($_POST["nombres"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $equipo_id = (int)($_POST["equipo_id"] ?? 0);
    $dorsal = (int)($_POST["dorsal"] ?? 0);
    $posicion = trim($_POST["posicion"] ?? "");
    $edad = (int)($_POST["edad"] ?? 0);

    if (
        $nombres === "" ||
        $apellidos === "" ||
        $equipo_id <= 0 ||
        $dorsal < 0 ||
        $edad <= 0 ||
        $posicion === ""
    ) {
        $mensaje = "Completa correctamente todos los campos.";
    } else {

        if ($id > 0) {

            $sql = "UPDATE jugadores
                    SET nombres=?, apellidos=?, equipo_id=?,
                        dorsal=?, posicion=?, edad=?
                    WHERE id=?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssiisii",
                $nombres,
                $apellidos,
                $equipo_id,
                $dorsal,
                $posicion,
                $edad,
                $id
            );

        } else {

            $sql = "INSERT INTO jugadores
                    (nombres, apellidos, equipo_id, dorsal, posicion, edad)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssiisi",
                $nombres,
                $apellidos,
                $equipo_id,
                $dorsal,
                $posicion,
                $edad
            );
        }

        if ($stmt->execute()) {
            header("Location: jugadores.php?estado=correcto");
            exit;
        } else {
            $mensaje = "Error al guardar. Verifica que el dorsal no esté repetido en el mismo equipo.";
        }

        $stmt->close();
    }
}

// ELIMINAR JUGADOR
if (isset($_GET["eliminar"])) {

    $id = (int)$_GET["eliminar"];

    $stmt = $conn->prepare(
        "DELETE FROM jugadores WHERE id=?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: jugadores.php?eliminado=1");
        exit;
    }

    $mensaje = "No se pudo eliminar el jugador.";
    $stmt->close();
}

// CARGAR DATOS PARA EDITAR
if (isset($_GET["editar"])) {

    $id = (int)$_GET["editar"];

    $stmt = $conn->prepare(
        "SELECT * FROM jugadores WHERE id=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $editar = $stmt->get_result()->fetch_assoc();

    $stmt->close();
}

// OBTENER EQUIPOS
$equipos = $conn->query(
    "SELECT id, nombre FROM equipos ORDER BY nombre"
);

// OBTENER JUGADORES
$sql = "SELECT j.*, e.nombre AS equipo
        FROM jugadores j
        INNER JOIN equipos e ON j.equipo_id = e.id
        ORDER BY j.id DESC";

$jugadores = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Jugadores</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <h2 class="mb-4">🏀 Gestión de Jugadores</h2>

    <?php if ($mensaje !== ""): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["estado"])): ?>
        <div class="alert alert-success">
            Jugador guardado correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["eliminado"])): ?>
        <div class="alert alert-success">
            Jugador eliminado correctamente.
        </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <div class="card shadow mb-4">

        <div class="card-header bg-dark text-white">
            <?= $editar ? "Editar jugador" : "Registrar nuevo jugador" ?>
        </div>

        <div class="card-body">

            <form method="POST" action="jugadores.php">

                <input type="hidden" name="id"
                    value="<?= htmlspecialchars($editar["id"] ?? "") ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres"
                            class="form-control" required
                            value="<?= htmlspecialchars($editar["nombres"] ?? "") ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos"
                            class="form-control" required
                            value="<?= htmlspecialchars($editar["apellidos"] ?? "") ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Equipo</label>

                        <select name="equipo_id" class="form-select" required>
                            <option value="">Seleccionar equipo</option>

                            <?php while ($equipo = $equipos->fetch_assoc()): ?>
                                <option value="<?= $equipo["id"] ?>"
                                    <?= (isset($editar["equipo_id"]) &&
                                    $editar["equipo_id"] == $equipo["id"])
                                    ? "selected" : "" ?>>

                                    <?= htmlspecialchars($equipo["nombre"]) ?>

                                </option>
                            <?php endwhile; ?>

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dorsal</label>
                        <input type="number" name="dorsal"
                            class="form-control" min="0" required
                            value="<?= htmlspecialchars($editar["dorsal"] ?? "") ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Posición</label>

                        <select name="posicion" class="form-select" required>
                            <option value="">Seleccionar posición</option>

                            <?php
                            $posiciones = [
                                "Base",
                                "Escolta",
                                "Alero",
                                "Ala-pívot",
                                "Pívot"
                            ];

                            foreach ($posiciones as $posicion):
                            ?>
                                <option value="<?= $posicion ?>"
                                    <?= (($editar["posicion"] ?? "") === $posicion)
                                    ? "selected" : "" ?>>
                                    <?= $posicion ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Edad</label>
                        <input type="number" name="edad"
                            class="form-control" min="1" required
                            value="<?= htmlspecialchars($editar["edad"] ?? "") ?>">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    <?= $editar ? "Actualizar jugador" : "Registrar jugador" ?>
                </button>

                <?php if ($editar): ?>
                    <a href="jugadores.php" class="btn btn-secondary">
                        Cancelar
                    </a>
                <?php endif; ?>

            </form>

        </div>
    </div>

    <!-- TABLA DE JUGADORES -->
    <div class="card shadow">

        <div class="card-header bg-dark text-white">
            Lista de jugadores registrados
        </div>

        <div class="card-body table-responsive">

            <table class="table table-striped table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Equipo</th>
                        <th>Dorsal</th>
                        <th>Posición</th>
                        <th>Edad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($jugadores && $jugadores->num_rows > 0): ?>

                    <?php while ($jugador = $jugadores->fetch_assoc()): ?>

                        <tr>
                            <td><?= $jugador["id"] ?></td>

                            <td><?= htmlspecialchars($jugador["nombres"]) ?></td>

                            <td><?= htmlspecialchars($jugador["apellidos"]) ?></td>

                            <td><?= htmlspecialchars($jugador["equipo"]) ?></td>

                            <td><?= $jugador["dorsal"] ?></td>

                            <td><?= htmlspecialchars($jugador["posicion"]) ?></td>

                            <td><?= $jugador["edad"] ?></td>

                            <td>
                                <a href="jugadores.php?editar=<?= $jugador["id"] ?>"
                                   class="btn btn-warning btn-sm">
                                    Editar
                                </a>

                                <a href="jugadores.php?eliminar=<?= $jugador["id"] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Eliminar este jugador?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8" class="text-center">
                            No hay jugadores registrados.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">
        Volver al inicio
    </a>

</div>

</body>
</html>
