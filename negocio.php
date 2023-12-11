<?php
// Incluir el archivo de configuración
require_once 'config.php';

// Consulta para obtener los datos del negocio
$sql = "SELECT * FROM negocio";
$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Obtener la primera fila (asumiendo que solo hay un registro de negocio)
    $negocio = $result->fetch_assoc();
} else {
    // Puedes manejar el caso en el que no hay datos del negocio
    $negocio = null;
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Datos del Negocio</title>
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
            text-align: center; /* Centra el contenido dentro del contenedor */
        }

        h2 {
            color: #333;
        }

        p {
            color: #555;
            margin-top: 20px;
        }

        .datos-negocio {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .datos-negocio p {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Datos del Negocio</h2>
            <p><strong><?php echo $negocio['nombre']; ?></strong></p>

        <?php if ($negocio !== null) : ?>
            <div class="datos-negocio">
                <p>Dirección: <strong><?php echo $negocio['direccion']; ?></strong></p>
                <p>- Teléfono: <strong><?php echo $negocio['telefono']; ?></strong></p>
                <p>- WhatsApp: <strong><?php echo $negocio['whatsapp']; ?></strong></p>
                <p>- Localidad: <strong><?php echo $negocio['localidad']; ?></strong></p>
            </div>
        <?php else : ?>
            <p>No hay datos de negocio disponibles en este momento.</p>
        <?php endif; ?>
    </div>
</body>
</html>