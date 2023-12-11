<?php
// Inicia la sesión
session_start();

// Verifica si hay una sesión de usuario activa
if (!isset($_SESSION['usuario'])) {
    // Redirige a la página de inicio de sesión si no hay sesión activa
    header('Location: login.php');
    exit();
}

// Obtén los datos del usuario de la sesión
$datosUsuario = $_SESSION['usuario'];

// Archivo de configuración y conexión a la base de datos
require_once 'config.php';

// HTML de la página
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Datos del Usuario</title>
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

        .usuario-info {
            float: right;
            margin-top: 10px;
            position: relative;
        }

        .usuario-info p {
            margin: 0;
            cursor: pointer;
        }

        .menu-desplegable {
            display: none;
            position: absolute;
            top: 25px;
            right: 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            padding: 5px 0;
            z-index: 1;
        }

        .menu-desplegable a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }

        .menu-desplegable a:hover {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="usuario-info" id="menuUsuario">
            <p onclick="toggleMenu()">Bienvenido, <strong><?php echo $datosUsuario['nombre']; ?></strong> &#9660;</p>
            <div class="menu-desplegable" id="menuDesplegable">
                <a href="modificar_datos_usuario.php">Administrar</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
        <h2>Datos del Usuario</h2>

        <!-- Mostrar otros datos del usuario si es necesario -->
        <p>Email: <?php echo $datosUsuario['correo']; ?></p>
        <!-- Agrega más detalles según tu estructura de datos de usuario -->

        <!-- Agrega más contenido según tus necesidades -->

    </div>

    <script>
        function toggleMenu() {
            var menuDesplegable = document.getElementById("menuDesplegable");
            menuDesplegable.style.display = (menuDesplegable.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>