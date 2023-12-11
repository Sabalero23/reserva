<?php
require_once '../config.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    echo "Acceso no autorizado.";
    exit();
}

// Lógica para agregar/quitar canchas
if (isset($_POST['agregar_cancha'])) {
    $nuevaCanchaNombre = $_POST['nueva_cancha_nombre'];

    // Verificar si la cancha ya existe
    $stmt = $conn->prepare("SELECT id FROM canchas WHERE nombre = ?");
    $stmt->bind_param("s", $nuevaCanchaNombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // La cancha ya existe, muestra un mensaje de error
        $mensaje = "La cancha '$nuevaCanchaNombre' ya existe.";
    } else {
        // La cancha no existe, procede a agregarla
        $stmt = $conn->prepare("INSERT INTO canchas (nombre, disponible) VALUES (?, true)");
        $stmt->bind_param("s", $nuevaCanchaNombre);

        if ($stmt->execute()) {
            $mensaje = "Cancha '$nuevaCanchaNombre' agregada con éxito.";
        } else {
            $mensaje = "Error al agregar la cancha: " . $conn->error;
        }
    }

    $stmt->close();
}

// Quitar Cancha
if (isset($_POST['quitar_cancha'])) {
    $canchaId = $_POST['cancha_id_quitar'];

    // Antes de eliminar la cancha, puedes realizar verificaciones adicionales si es necesario.

    $sql = "DELETE FROM canchas WHERE id = '$canchaId'";
    if ($conn->query($sql) === TRUE) {
        $mensaje = "Cancha con ID '$canchaId' eliminada con éxito.";
    } else {
        $mensaje = "Error al eliminar la cancha: " . $conn->error;
    }
}

// Mostrar Canchas Actuales
$result = $conn->query("SELECT * FROM canchas");
$canchas = $result->fetch_all(MYSQLI_ASSOC);

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos.css">
    <title>Administración de Canchas</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            color: #333;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            color: #0066cc;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        select, input, button {
            width: 60%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: #0066cc;
            width: 150px;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #004080;
        }

        .mensaje {
            margin-top: 20px;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="index.php">Volver a la Página Principal</a></p>
        <br>
        <h2>Administración de Canchas</h2>

        <?php
        if (isset($mensaje)) {
            if (strpos($mensaje, 'Error') !== false) {
                echo "<div class='error mensaje'>{$mensaje}</div>";
            } else {
                echo "<div class='mensaje'>{$mensaje}</div>";
            }
        }
        ?>

        <h3>Canchas Actuales:</h3>
        <ul>
            <?php
            foreach ($canchas as $cancha) {
                echo "<li>{$cancha['nombre']}</li>";
            }
            ?>
        </ul>

        <h3>Agregar Nueva Cancha:</h3>
        <form method='post' action='agregar_quitar_canchas.php'>
            <label for="nueva_cancha_nombre">Nombre de la nueva cancha:</label>
            <input type='text' name='nueva_cancha_nombre' required>
            <button type='submit' name='agregar_cancha'>Agregar</button>
        </form>

        <h3>Quitar Cancha:</h3>
        <form method='post' action='agregar_quitar_canchas.php'>
            <label for="cancha_id_quitar">Seleccione la cancha a quitar:</label>
            <select name='cancha_id_quitar'>
                <?php
                foreach ($canchas as $cancha) {
                    echo "<option value='{$cancha['id']}'>{$cancha['nombre']}</option>";
                }
                ?>
            </select>
            <button type='submit' name='quitar_cancha'>Quitar</button>
        </form>
    </div>
</body>
</html>