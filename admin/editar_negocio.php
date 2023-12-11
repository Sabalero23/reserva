<?php
require_once '../config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $whatsapp = $_POST['whatsapp'];
    $localidad = $_POST['localidad'];

    // Update the business information in the 'negocio' table
    $stmt = $conn->prepare("UPDATE negocio SET nombre=?, direccion=?, telefono=?, whatsapp=?, localidad=? WHERE id=1");
    $stmt->bind_param("sssss", $nombre, $direccion, $telefono, $whatsapp, $localidad);

    if ($stmt->execute()) {
        $mensaje = "Datos del negocio actualizados con éxito.";
    } else {
        $mensaje = "Error al actualizar los datos del negocio: " . $stmt->error;
    }

    $stmt->close();
}

// Retrieve the current business information
$sql = "SELECT * FROM negocio WHERE id=1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $negocio = $result->fetch_assoc();
} else {
    // If no data exists, you can set default values or handle it as needed
    $negocio = [
        'nombre' => '',
        'direccion' => '',
        'telefono' => '',
        'whatsapp' => '',
        'localidad' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='../estilos.css'>
    <title>Editar Datos del Negocio</title>
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

        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: #0066cc;
            color: #fff;
            padding: 10px;
            width: 300px;
            border: none;
            cursor: pointer;
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
    <div class='container'>
        <h2>Editar Datos del Negocio</h2>

        <?php
        if (isset($mensaje)) {
            if (strpos($mensaje, 'Error') !== false) {
                echo "<div class='error mensaje'>{$mensaje}</div>";
            } else {
                echo "<div class='mensaje'>{$mensaje}</div>";
            }
        }
        ?>

        <form method='post' action='editar_negocio.php'>
            <label for='nombre'>Nombre del Negocio:</label>
            <input type='text' name='nombre' value='<?php echo $negocio['nombre']; ?>' required>

            <label for='direccion'>Dirección:</label>
            <input type='text' name='direccion' value='<?php echo $negocio['direccion']; ?>' required>

            <label for='telefono'>Teléfono:</label>
            <input type='text' name='telefono' value='<?php echo $negocio['telefono']; ?>' required>

            <label for='whatsapp'>Número de WhatsApp:</label>
            <input type='text' name='whatsapp' value='<?php echo $negocio['whatsapp']; ?>' required>

            <label for='localidad'>Localidad:</label>
            <input type='text' name='localidad' value='<?php echo $negocio['localidad']; ?>' required>

            <button type='submit'>Guardar Cambios</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>