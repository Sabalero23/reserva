<?php
require_once 'config.php';

// Verificar si el formulario de registro ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Validar y escapar los datos (puedes agregar más validaciones según sea necesario)
    $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);
    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
    $telefono = filter_var($telefono, FILTER_SANITIZE_STRING);

    // Verificar si la dirección de correo es válida
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "La dirección de correo electrónico no es válida. Por favor, inténtalo de nuevo.";
    } elseif ($contrasena !== $confirmar_contrasena) {
        $mensaje = "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
    } else {
        // Hash de la contraseña utilizando password_hash
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // Consulta preparada para evitar inyección de SQL
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, telefono, contrasena, rol) VALUES (?, ?, ?, ?, 'Usuario')");
        $stmt->bind_param("ssss", $nombre, $correo, $telefono, $contrasena_hash);

        if ($stmt->execute()) {
            $mensaje = "Usuario registrado con éxito. Ahora puedes iniciar sesión.";
        } else {
            $mensaje = "Error al registrar el usuario: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Registro de Usuario</title>
    <style>
        /* Agregar estilos específicos para esta página si es necesario */
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
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #0066cc;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        label, input, button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: #0066cc;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #004080;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>

        <?php
        if (isset($mensaje)) {
            echo "<p>{$mensaje}</p>";
        }
        ?>

        <!-- Formulario de registro -->
        <form method="post" action="registrar.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="correo">Correo:</label>
            <input type="text" id="correo" name="correo" required>

            <label for="telefono">Número de Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label for="confirmar_contrasena">Confirmar Contraseña:</label>
            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>

            <button type="submit">Registrar</button>
        </form>

        <p><a href="login.php">Ya tienes una cuenta? Inicia sesión aquí.</a></p>
    </div>
</body>
</html>