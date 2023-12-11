<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Reemplaza 'iniciar_sesion.php' con la página de inicio de sesión de tu aplicación
    exit();
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

$stmt = $conn->prepare("SELECT horarios.id, horario_inicio, horario_fin, IFNULL(reservas.id, 0) as reservado
                       FROM horarios
                       LEFT JOIN reservas ON horarios.id = reservas.horario_id AND reservas.cancha_id = ?
                       WHERE horarios.suspendido = 0 AND horarios.estado = 1");
$stmt->bind_param("i", $cancha_id);
$stmt->execute();
$horarios_disponibles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

        <form method="post" action="procesar_reserva.php">
            <!-- Incluir el campo oculto para enviar el ID de la cancha -->
            <input type="hidden" name="cancha_id" value="<?php echo $cancha_id; ?>">

            <label for="horario_id">Seleccionar Horario:</label>
            <select name="horario_id" required>
                <?php foreach ($horarios_disponibles as $horario) : ?>
                    <?php
                        $estado = ($horario['reservado'] > 0) ? 'Reservado' : 'Libre';
                        $color = ($horario['reservado'] > 0) ? 'reservado' : 'libre';
                    ?>
                    <option value="<?php echo $horario['id']; ?>" class="<?php echo $color; ?>"><?php echo $horario['horario_inicio'] . ' - ' . $horario['horario_fin'] . ' (' . $estado . ')'; ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" name="reservar_horario" value="Reservar Horario">
        </form>

        <p><a href="index.php">Volver a la Página Principal</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>