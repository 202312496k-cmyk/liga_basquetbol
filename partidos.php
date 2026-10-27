
<?php

require_once __DIR__ . "../configuracion/conexcion.php";
require_once __DIR__ . "../funciones.php";

$mensaje = $_GET["mensaje"] ?? "";

$mensajes = [
    "eliminado" => "Partido eliminado correctamente.",
    "actualizado" => "Marcador actualizado correctamente.",
    "error" => "Ocurrió un error al realizar la operación."
];

// Procesar formularios
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        header("Location: partidos.php?mensaje=error");
        exit;
    }

    // Validar token CSRF si está disponible
    if (function_exists("validar_csrf")) {
        if (!validar_csrf($_POST["csrf_token"] ?? "")) {
            header("Location: partidos.php?mensaje=error");
            exit;
        }
    }

    // Eliminar partido
    if (isset($_POST["eliminar"])) {
        $stmt = $conn->prepare("DELETE FROM partidos WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $correcto = $stmt->execute();
            $stmt->close();

            header(
                "Location: partidos.php?mensaje=" .
                ($correcto ? "eliminado" : "error")
            );
        } else {
            header("Location: partidos.php?mensaje=error");
        }

        exit;
    }

    // Actualizar marcador
    $local = filter_input(INPUT_POST, "puntos_local", FILTER_VALIDATE_INT);
    $visitante = filter_input(
        INPUT_POST,
        "puntos_visitante",
        FILTER_VALIDATE_INT
    );

    if (
        $local === false || $local === null || $local < 0 ||
        $visitante === false || $visitante === null || $visitante < 0
    ) {
        header("Location: partidos.php?mensaje=error");
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE partidos
         SET puntos_local = ?,
             puntos_visitante = ?,
             estado = 'Finalizado'
         WHERE id = ?"
    );

    if ($stmt) {
        $stmt->bind_param("iii", $local, $visitante, $id);
        $correcto = $stmt->execute();
        $stmt->close();

        header(
            "Location: partidos.php?mensaje=" .
            ($correcto ? "actualizado" : "error")
        );
    } else {
        header("Location: partidos.php?mensaje=error");
    }

    exit;
}

// Consultar partidos
$sql = "SELECT
            p.*,
            el.nombre AS local_nombre,
            ev.nombre AS visitante_nombre
        FROM partidos p
        INNER JOIN equipos el ON el.id = p.equipo_local
        INNER JOIN equipos ev ON ev.id = p.equipo_visitante
        ORDER BY p.fecha DESC";

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error al consultar los partidos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de partidos</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1>Control de partidos</h1>

        <div>
            <a href="index.php" class="btn btn-secondary">
                Inicio
            </a>

            <a href="crear_partido.php" class="btn btn-success">
                + Programar partido
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if (isset($mensajes[$mensaje])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= h($mensajes[$mensaje]) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"
            ></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de partidos -->
    <div class="card shadow-sm">
        <div class="card-body">

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
                    <?php if ($resultado->num_rows === 0): ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay partidos registrados.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php while ($p = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?= h($p["fecha"]) ?>
                                </td>

                                <td>
                                    <?= h($p["local_nombre"]) ?>
                                </td>

                                <td>
                                    <form
                                        method="POST"
                                        action="partidos.php"
                                        class="d-flex align-items-center gap-1"
                                    >
                                        <?php if (function_exists("csrf_token")): ?>
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= h(csrf_token()) ?>"
                                            >
                                        <?php endif; ?>

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $p["id"] ?>"
                                        >

                                        <input
                                            type="number"
                                            min="0"
                                            name="puntos_local"
                                            class="form-control"
                                            style="width: 80px"
                                            value="<?= $p["puntos_local"] === null ? "" : (int) $p["puntos_local"] ?>"
                                            aria-label="Puntos del equipo local"
                                            required
                                        >

                                        <span>-</span>

                                        <input
                                            type="number"
                                            min="0"
                                            name="puntos_visitante"
                                            class="form-control"
                                            style="width: 80px"
                                            value="<?= $p["puntos_visitante"] === null ? "" : (int) $p["puntos_visitante"] ?>"
                                            aria-label="Puntos del equipo visitante"
                                            required
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-primary btn-sm"
                                        >
                                            Guardar
                                        </button>
                                    </form>
                                </td>

                                <td>
                                    <?= h($p["visitante_nombre"]) ?>
                                </td>

                                <td>
                                    <?php if ($p["estado"] === "Finalizado"): ?>
                                        <span class="badge bg-success">
                                            Finalizado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <?= h($p["estado"]) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <form
                                        method="POST"
                                        action="partidos.php"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este partido?');"
                                    >
                                        <?php if (function_exists("csrf_token")): ?>
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= h(csrf_token()) ?>"
                                            >
                                        <?php endif; ?>

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $p["id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="eliminar"
                                            value="1"
                                            class="btn btn-danger btn-sm"
                                        >
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

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/animaciones.js"></script>

</body>
</html>
