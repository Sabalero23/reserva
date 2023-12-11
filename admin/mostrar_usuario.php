<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: login.php'); // Redirige a la página de inicio de sesión si no es un administrador
    exit();
}

// Verificar si se proporciona un ID de usuario válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$usuario_id = $_GET['id'];

// Consultar información del usuario
try {
    $stmtUsuario = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmtUsuario->bind_param("i", $usuario_id);
    $stmtUsuario->execute();
    $usuario = $stmtUsuario->get_result()->fetch_assoc();
    $stmtUsuario->close();
} catch (Exception $e) {
    // Manejar errores de manera más robusta, como redirigir a una página de error
    echo "Error al consultar la base de datos: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Detalle del Usuario</title>
    <style>
        /* Agregar estilos específicos para esta página si es necesario */
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

        p {
            color: #555;
            margin-bottom: 10px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detalles del Usuario</h2>

        <?php
        if ($usuario) {
            echo "<p>ID: {$usuario['id']}</p>";
            echo "<p>Nombre: {$usuario['nombre']}</p>";
            echo "<p>Correo Electrónico: {$usuario['correo']}</p>";

            // Agrega más detalles según sea necesario
            if (isset($usuario['telefono'])) {
                echo "<p>Teléfono: {$usuario['telefono']}</p>";
            }

            if (isset($usuario['rol'])) {
                echo "<p>Rol: {$usuario['rol']}</p>";
            }

            // Puedes agregar más detalles aquí
        } else {
            echo "<p>No se encontró información del usuario.</p>";
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