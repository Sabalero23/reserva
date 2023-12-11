<?php
require_once '../config.php';

// Asegúrate de iniciar la sesión al principio del script
session_start();

if (isset($_SESSION['usuario'])) {
    // Verificar el rol del usuario
    $rol = $_SESSION['usuario']['rol'];

    if ($rol == 'admin') {
        // Si el usuario es un administrador, redirige al index.php
        header('Location: index.php');
        exit();
    } else {
        // Si el usuario es un 'usuario', muestra un mensaje modal
        $nombreUsuario = $_SESSION['usuario']['nombre']; // Ajusta esto según tu estructura de datos
        echo "<script>
                alert('$nombreUsuario No tienes los permisos de Administrador');
                window.location.href = '../index.php';
              </script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Utilizar sentencias preparadas para evitar SQL injection
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña utilizando password_verify
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario'] = $usuario;

            // Verificar el rol del usuario después de iniciar sesión
            if ($usuario['rol'] == 'admin') {
                // Si el usuario es un administrador, redirige al index.php
                header('Location: index.php');
                exit();
            } else {
                // Si el usuario es un 'usuario', muestra un mensaje modal
                $nombreUsuario = $usuario['nombre']; // Ajusta esto según tu estructura de datos
                echo "<script>
                        alert('$nombreUsuario No tienes los permisos de Administrador');
                        window.location.href = '../index.php';
                      </script>";
                exit();
            }
        } else {
            // Mensaje de error genérico
            echo "Inicio de sesión fallido";
        }
    } else {
        // Mensaje de error genérico
        echo "Inicio de sesión fallido. ¿No tienes una cuenta? <a href='registrar.php'>Crear cuenta nueva</a>.";
    }

    // Cierra la sentencia preparada
    $stmt->close();
}

// Cierra la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos.css">
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px; /* Reduje el ancho máximo a 600px, puedes ajustar este valor según tus necesidades */
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

h2 {
    color: #333;
    text-align: center; /* Añade alineación al centro para el título */
    font-size: 34px; /* Puedes ajustar este valor según tus preferencias */
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
    <h2>Iniciar Sesión - Sólo Administradores</h2>
    <form method="post" action="login.php">
        <label for="correo">Correo:</label>
        <input type="text" id="correo" name="correo" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <input type="submit" value="Iniciar Sesión">
    </form>
    
</body>
</html>