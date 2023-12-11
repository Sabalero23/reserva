<?php
require_once '../config.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: login.php'); // Redirige a la página de inicio de sesión si no es un administrador
    exit();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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
            margin-top: 20px;
        }

        h2 {
            color: #0066cc;
            margin-bottom: 20px;
        }

        h3 {
            color: #004080;
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
            text-decoration: none;
            color: #0066cc;
            transition: color 0.3s ease;
            font-weight: bold;
        }

        a:hover {
            color: #004080;
        }

        p {
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Panel de Administración</h2>
        <p>Bienvenido, <?php echo $_SESSION['usuario']['nombre']; ?>!</p>

        <h3>Administrar:</h3>
        <ul>
            <li><a href="editar_negocio.php">Empresa</a></li>
            <li><a href="horarios.php">Horarios</a></li>
            <li><a href="reservas.php">Reservas</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="agregar_quitar_canchas.php">Agregar/Quitar Canchas</a></li>
            <!-- Agrega más enlaces según sea necesario -->
        </ul>

        <p><a href="../index.php">Volver al Inicio</a></p>
    </div>

</body>
</html>