<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Almacenar temporalmente la fecha seleccionada en la sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fecha_reserva'])) {
    $_SESSION['fecha_reserva'] = $_POST['fecha_reserva'];
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $cancha_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM canchas WHERE id = ?");
    $stmt->bind_param("i", $cancha_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $cancha = $result->fetch_assoc();
    } else {
        header('Location: index.php');
        exit();
    }

    $stmt->close();
} else {
    header('Location: index.php');
    exit();
}

// Obtener mensaje si está presente en la URL
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';

// Procesamiento del formulario para mostrar horarios disponibles
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_reserva = $_SESSION['fecha_reserva'];

    // Obtener horarios disponibles para la fecha seleccionada
    $stmtHorarios = $conn->prepare("SELECT h.id, h.horario_inicio, h.horario_fin
                                   FROM horarios h
                                   WHERE h.suspendido = 0 AND h.estado = 1
                                   AND NOT EXISTS (
                                       SELECT 1
                                       FROM reservas r
                                       WHERE r.fecha_reserva = ? AND r.horario_id = h.id AND r.cancha_id = ?
                                   )");
    $stmtHorarios->bind_param("si", $fecha_reserva, $cancha_id);
    $stmtHorarios->execute();
    $horarios_disponibles = $stmtHorarios->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtHorarios->close();
}

// Procesamiento del formulario para reservar horario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reservar_horario'])) {
    $horario_id = $_POST['horario_id'];

    // Verificar si el horario sigue disponible
    $stmtVerificar = $conn->prepare("SELECT 1
                                     FROM horarios h
                                     WHERE h.suspendido = 0 AND h.estado = 1
                                     AND NOT EXISTS (
                                         SELECT 1
                                         FROM reservas r
                                         WHERE r.fecha_reserva = ? AND r.horario_id = h.id AND r.cancha_id = ?
                                     ) AND h.id = ?");
    $stmtVerificar->bind_param("sii", $fecha_reserva, $cancha_id, $horario_id);
    $stmtVerificar->execute();
    $horario_disponible = $stmtVerificar->fetch();
    $stmtVerificar->close();

    if ($horario_disponible) {
        // Insertar reserva en la base de datos
$usuario_id = $_SESSION['usuario']['id'];

$stmtInsertar = $conn->prepare("INSERT INTO reservas (cancha_id, usuario_id, fecha_reserva, horario_id)
                               VALUES (?, ?, DATE(?), ?)");
$stmtInsertar->bind_param("iisi", $cancha_id, $usuario_id, $fecha_reserva, $horario_id);
$stmtInsertar->execute();
$stmtInsertar->close();

        // Redirigir con un mensaje de éxito
        header('Location: reservar.php?id=' . $cancha_id . '&mensaje=Reserva realizada con éxito');
        exit();
    } else {
        // Redirigir con un mensaje de error
        header('Location: reservar.php?id=' . $cancha_id . '&mensaje=Error al realizar la reserva. Por favor, inténtalo nuevamente.');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Reservar Cancha</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .reservado {
            color: red;
        }

        .libre {
            color: green;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        p {
            color: #555;
            margin-top: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reservar Cancha: <?php echo $cancha['nombre']; ?></h2>

        <?php
        if (!empty($mensaje)) {
            echo "<p>$mensaje</p>";
        }
        ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $cancha_id; ?>">
            <!-- Incluir el campo oculto para enviar el ID de la cancha -->
            <input type="hidden" name="cancha_id" value="<?php echo $cancha_id; ?>">

            <!-- Nuevo campo para seleccionar la fecha de reserva -->
            <label for="fecha_reserva">Seleccionar Fecha:</label>
            <input type="date" name="fecha_reserva" value="<?php echo isset($_SESSION['fecha_reserva']) ? $_SESSION['fecha_reserva'] : ''; ?>" required>
            <input type="submit" value="Mostrar Horarios Disponibles">

            <!-- Seleccionar los horarios disponibles para la fecha -->
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($horarios_disponibles)) {
                echo '<label for="horario_id">Seleccionar Horario:</label>';
                echo '<select name="horario_id" required>';
                foreach ($horarios_disponibles as $horario) {
                    echo '<option value="' . $horario['id'] . '">' . $horario['horario_inicio'] . ' - ' . $horario['horario_fin'] . '</option>';
                }
                echo '</select>';
                echo '<input type="submit" name="reservar_horario" value="Reservar Horario">';
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && empty($horarios_disponibles)) {
                echo '<p>No hay horarios disponibles para la fecha seleccionada.</p>';
            }
            ?>
        </form>

        <p><a href="index.php">Volver a la Página Principal</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
