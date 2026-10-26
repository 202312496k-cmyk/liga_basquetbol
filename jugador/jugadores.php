
<?php
require_once __DIR__ . "/configuracion/conexcion.php";
require_once __DIR__ . "/includes/funciones.php";

$mensaje = $_GET["mensaje"] ?? "";

$mensajes = [
    "creado" => "Jugador registrado correctamente.",
    "actualizado" => "Jugador actualizado correctamente.",
    "eliminado" => "Jugador eliminado correctamente.",
    "duplicado" => "Ese dorsal ya está registrado para este equipo.",
    "error" => "Verifica los datos ingresados."
];

$equipos = $conn->query(
    "SELECT id, nombre FROM equipos ORDER BY nombre"
);

$sql = "SELECT j.id, j.equipo_id, j.nombres, j.apellidos,
               j.dorsal, j.posicion, j.edad,
               e.nombre AS equipo
        FROM jugadores j
        INNER JOIN equipos e ON j.equipo_id = e.id
        ORDER BY j.id DESC";

$jugadores = $conn->query($sql);

$editar = null;

if (isset($_GET["editar"])) {
    $idEditar = (int)$_GET["editar"];

    if ($idEditar > 0) {
        $stmt = $conn->prepare(
            "SELECT id, equipo_id, nombres, apellidos,
                    dorsal, posicion, edad
             FROM jugadores
             WHERE id = ?"
        );

        $stmt->bind_param("i", $idEditar);
        $stmt->execute();

        $editar = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

$posiciones = [
    "Base",
    "Escolta",
    "Alero",
    "Ala-pívot",
    "Pívot"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestión de jugadores</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de jugadores</h1>
        <a href="index.php" class="btn btn-secondary">
            Volver al inicio
        </a>
    </div>

    <?php if (isset($mensajes[$mensaje])): ?>
        <div class="alert alert-info">
            <?= h($mensajes[$mensaje]) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <?= $editar ? "Editar jugador" : "Registrar jugador" ?>
        </div>

        <div class="card-body">

            <?php if ($equipos->num_rows === 0): ?>

                <div class="alert alert-warning">
                    Primero debes registrar un equipo.
                    <a href="equipos.php">Ir a equipos</a>
                </div>

            <?php else: ?>

                <form action="guardar_jugador.php" method="POST">

                    <input type="hidden"
                           name="csrf_token"
                           value="<?= h(csrf_token()) ?>">

                    <input type="hidden"
                           name="id"
                           value="<?= (int)($editar["id"] ?? 0) ?>">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nombres</label>
                            <input type="text"
                                   name="nombres"
                                   class="form-control"
                                   maxlength="100"
                                   required
                                   value="<?= h($editar["nombres"] ?? "") ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text"
                                   name="apellidos"
                                   class="form-control"
                                   maxlength="100"
                                   required
                                   value="<?= h($editar["apellidos"] ?? "") ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Equipo</label>

                            <select name="equipo_id"
                                    class="form-select"
                                    required>

                                <option value="">Selecciona un equipo</option>

                                <?php while ($equipo = $equipos->fetch_assoc()): ?>
                                    <option
                                        value="<?= (int)$equipo["id"] ?>"
                                        <?= (int)($editar["equipo_id"] ?? 0) === (int)$equipo["id"] ? "selected" : "" ?>>

                                        <?= h($equipo["nombre"]) ?>

                                    </option>
                                <?php endwhile; ?>

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Dorsal</label>
                            <input type="number"
                                   name="dorsal"
                                   class="form-control"
                                   min="0"
                                   max="99"
                                   required
                                   value="<?= h($editar["dorsal"] ?? "") ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Edad</label>
                            <input type="number"
                                   name="edad"
                                   class="form-control"
                                   min="1"
                                   max="100"
                                   required
                                   value="<?= h($editar["edad"] ?? "") ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Posición</label>

                            <select name="posicion"
                                    class="form-select"
                                    required>

                                <?php foreach ($posiciones as $posicion): ?>
                                    <option
                                        value="<?= h($posicion) ?>"
                                        <?= ($editar["posicion"] ?? "Alero") === $posicion ? "selected" : "" ?>>

                                        <?= h($posicion) ?>

                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <?= $editar ? "Guardar cambios" : "Registrar jugador" ?>
                            </button>

                            <?php if ($editar): ?>
                                <a href="jugadores.php"
                                   class="btn btn-secondary">
                                    Cancelar
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </form>

            <?php endif; ?>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Lista de jugadores
        </div>

        <div class="card-body table-responsive">

            <table class="table table-striped table-hover align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jugador</th>
                        <th>Equipo</th>
                        <th>Dorsal</th>
                        <th>Posición</th>
                        <th>Edad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($jugadores->num_rows === 0): ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                No hay jugadores registrados.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php while ($jugador = $jugadores->fetch_assoc()): ?>

                            <tr>
                                <td><?= (int)$jugador["id"] ?></td>

                                <td>
                                    <?= h($jugador["nombres"] . " " . $jugador["apellidos"]) ?>
                                </td>

                                <td><?= h($jugador["equipo"]) ?></td>
                                <td><?= (int)$jugador["dorsal"] ?></td>
                                <td><?= h($jugador["posicion"]) ?></td>
                                <td><?= (int)$jugador["edad"] ?></td>

                                <td>
                                    <a class="btn btn-sm btn-warning"
                                       href="jugadores.php?editar=<?= (int)$jugador["id"] ?>">
                                        Editar
                                    </a>

                                    <form action="eliminar_jugador.php"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este jugador?')">

                                        <input type="hidden"
                                               name="csrf_token"
                                               value="<?= h(csrf_token()) ?>">

                                        <input type="hidden"
                                               name="id"
                                               value="<?= (int)$jugador["id"] ?>">

                                        <button type="submit"
                                                class="btn btn-sm btn-danger">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
