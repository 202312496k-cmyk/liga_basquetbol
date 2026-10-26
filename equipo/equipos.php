
<?php
require_once __DIR__ . "/configuracion/conexcion.php";
require_once __DIR__ . "/includes/funciones.php";

// Mensaje recibido desde la URL
$mensaje = $_GET["mensaje"] ?? "";

// Mensajes que se mostrarán al usuario
$mensajes = [
    "creado" => "Equipo registrado correctamente.",
    "actualizado" => "Equipo actualizado correctamente.",
    "eliminado" => "Equipo eliminado correctamente.",
    "error" => "Ocurrió un error al realizar la operación.",
    "duplicado" => "Ya existe un equipo con ese nombre."
];

// Consultar equipos y contar sus jugadores
$sql = "SELECT
            e.id,
            e.nombre,
            e.ciudad,
            e.entrenador,
            COUNT(j.id) AS total_jugadores
        FROM equipos e
        LEFT JOIN jugadores j ON j.equipo_id = e.id
        GROUP BY e.id, e.nombre, e.ciudad, e.entrenador
        ORDER BY e.id DESC";

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error al consultar los equipos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Equipos</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Equipos</h1>

        <a href="index.php" class="btn btn-secondary">
            Volver al inicio
        </a>
    </div>

    <!-- Mensajes de operación -->
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

    <!-- Formulario para registrar un equipo -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Registrar equipo</h5>
        </div>

        <div class="card-body">
            <form action="guardar_equipo.php" method="POST">

                <?php if (function_exists("csrf_token")): ?>
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= h(csrf_token()) ?>"
                    >
                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="nombre" class="form-label">
                            Nombre del equipo
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            name="nombre"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="ciudad" class="form-label">
                            Ciudad
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="ciudad"
                            name="ciudad"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="entrenador" class="form-label">
                            Entrenador
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="entrenador"
                            name="entrenador"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            Registrar equipo
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de equipos -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Equipos registrados</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>Entrenador</th>
                            <th>Jugadores</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($resultado->num_rows > 0): ?>

                        <?php while ($equipo = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= (int) $equipo["id"] ?></td>

                                <td><?= h($equipo["nombre"]) ?></td>

                                <td><?= h($equipo["ciudad"]) ?></td>

                                <td><?= h($equipo["entrenador"]) ?></td>

                                <td>
                                    <span class="badge bg-primary">
                                        <?= (int) $equipo["total_jugadores"] ?>
                                    </span>
                                </td>

                                <td>
                                    <a
                                        href="editar_equipo.php?id=<?= (int) $equipo["id"] ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="eliminar_equipo.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');"
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
                                            value="<?= (int) $equipo["id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay equipos registrados.
                            </td>
                        </tr>

                    <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/animaciones.js"></script>

</body>
</html>
