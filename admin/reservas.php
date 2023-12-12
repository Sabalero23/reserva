<?php
require_once '../config.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: login.php'); // Redirige a la página de inicio de sesión si no es un administrador
    exit();
}

// Consultar reservas
try {
    // Consultar reservas con información del usuario, incluyendo la fecha de reserva
    $stmtReservas = $conn->prepare("SELECT r.id as reserva_id, c.nombre as cancha_nombre, h.horario_inicio, h.horario_fin, r.fecha_reserva, u.id as usuario_id, u.nombre as usuario_nombre
                                    FROM reservas r
                                    INNER JOIN canchas c ON r.cancha_id = c.id
                                    INNER JOIN horarios h ON r.horario_id = h.id
                                    INNER JOIN usuarios u ON r.usuario_id = u.id
                                    ORDER BY r.fecha_reserva DESC");
    $stmtReservas->execute();
    $reservas = $stmtReservas->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // Manejar errores
    echo "Error al consultar la base de datos: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos.css">
    <title>Reservas</title>
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

        h3 {
            color: #555;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            color: #555;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reservas</h2>

        <?php
        // Mostrar reservas con información del usuario
        if (!empty($reservas)) {
            echo "<h3>Reservas:</h3>";
            echo "<table>";
            echo "<tr><th>Cancha</th><th>Fecha</th><th>Horario</th><th>Reservado por</th></tr>";

            foreach ($reservas as $reserva) {
                echo "<tr>";
                echo "<td>{$reserva['cancha_nombre']}</td>";
                echo "<td>" . date('d/m/Y', strtotime($reserva['fecha_reserva'])) . "</td>";
                echo "<td>{$reserva['horario_inicio']} a {$reserva['horario_fin']}</td>";
                echo "<td><a href='mostrar_usuario.php?id={$reserva['usuario_id']}'>{$reserva['usuario_nombre']}</a></td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No hay reservas realizadas.</p>";
        }
        ?>

        <p><a href="index.php">Volver a la Página Principal</a></p>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
