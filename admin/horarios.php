<?php
require_once '../config.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php'); // Redirige a la página de inicio de sesión si no es un administrador
    exit();
}


// Verificar la conexión a la base de datos
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Manejar la solicitud POST para agregar horario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_horario'])) {
    // Validar y escapar los datos del formulario
    $horarioInicio = $conn->real_escape_string($_POST['horario_inicio']);
    $horarioFin = $conn->real_escape_string($_POST['horario_fin']);

    // Verificar si el horario ya existe
    $sql = $conn->prepare("SELECT * FROM horarios WHERE horario_inicio = ? AND horario_fin = ?");
    $sql->bind_param("ss", $horarioInicio, $horarioFin);
    $sql->execute();
    $result = $sql->get_result();

    if (!$result) {
        die("Error en la consulta: " . $sql->error);
    }

    if ($result->num_rows === 0) {
        // Consulta preparada para evitar SQL injection
        $sql = $conn->prepare("INSERT INTO horarios (horario_inicio, horario_fin, suspendido, estado) VALUES (?, ?, 0, 1)");
        $sql->bind_param("ss", $horarioInicio, $horarioFin);

        if ($sql->execute()) {
            echo "Horario agregado con éxito.";
        } else {
            die("Error al agregar el horario: " . $sql->error);
        }

        $sql->close();
    } else {
        echo "El horario ya existe.";
    }
}

// Manejar la solicitud GET para activar/desactivar horarios
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && isset($_GET['id'])) {
    $idHorario = $conn->real_escape_string($_GET['id']);

    if ($_GET['action'] === 'activar') {
        $conn->query("UPDATE horarios SET estado = 1 WHERE id = $idHorario");
    } elseif ($_GET['action'] === 'desactivar') {
        $conn->query("UPDATE horarios SET estado = 0 WHERE id = $idHorario");
    }
}

// Función para obtener y mostrar los horarios existentes
function mostrarHorarios() {
    global $conn;

    // Verificar la conexión a la base de datos
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM horarios";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        echo "<h2>Información de Horarios</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Hora de Inicio</th><th>Hora de Fin</th><th>Estado</th><th>Acciones</th></tr>";

while ($row = $result->fetch_assoc()) {
    $estado = ($row['estado'] == 1) ? 'Activo' : 'Inactivo';
    echo "<tr>";
    echo "<td>{$row['horario_inicio']}</td>";
    echo "<td>{$row['horario_fin']}</td>";
    echo "<td>{$estado}</td>";
    echo "<td>";
    echo "<form method='get' action='horarios.php' style='display: inline;'>";
    echo "<input type='hidden' name='id' value='{$row['id']}'>";
    
    if ($row['estado'] == 1) {
        echo "<button type='submit' name='action' value='desactivar'>Desactivar</button>";
    } else {
        echo "<button type='submit' name='action' value='activar'>Activar</button>";
    }

    echo "</form>";
    echo "</td>";
    echo "</tr>";
}


        echo "</table>";
    } else {
        echo "<p>No hay horarios disponibles.</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos.css">
    <title>Información de Horarios</title>
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
        text-align: center; /* Añade alineación al centro para el título */
        font-size: 24px; /* Puedes ajustar este valor según tus preferencias */
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
        <p><a href="index.php">Volver a la Página Principal</a></p>
        <br>
        <h2>Agregar Horario</h2>

        <form method="post" action="horarios.php">
            <label for="horario_inicio">Hora de Inicio:</label>
            <input type="time" id="horario_inicio" name="horario_inicio" required>

            <label for="horario_fin">Hora de Fin:</label>
            <input type="time" id="horario_fin" name="horario_fin" required>

            <input type="submit" name="agregar_horario" value="Agregar Horario">
        </form>

        <hr>

        <?php mostrarHorarios(); ?>
    </div>
</body>
</html>