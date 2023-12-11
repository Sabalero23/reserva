<?php
// Establecer la configuración para mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Inicializar la variable para almacenar los datos del usuario
$datosUsuario = null;
$mensaje = null;

// Verificar si hay una sesión de usuario activa
if (isset($_SESSION['usuario'])) {
    $idUsuario = $_SESSION['usuario']['id'];

    // Consulta para obtener los datos del usuario
    $sqlUsuario = "SELECT * FROM usuarios WHERE id = $idUsuario";
    $resultUsuario = $conn->query($sqlUsuario);

    // Verificar si hay resultados
    if ($resultUsuario->num_rows > 0) {
        $datosUsuario = $resultUsuario->fetch_assoc();
    }
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la contraseña anterior del formulario
    $contrasenaAnterior = isset($_POST['contrasena_anterior']) ? $_POST['contrasena_anterior'] : '';

    // Verificar si la contraseña anterior es correcta
    $idUsuario = $_SESSION['usuario']['id'];
    $sqlContrasena = "SELECT contrasena FROM usuarios WHERE id = $idUsuario";
    $resultContrasena = $conn->query($sqlContrasena);

    if ($resultContrasena) {
        $row = $resultContrasena->fetch_assoc();
        $contrasenaAlmacenada = $row['contrasena'];

        // Verificar la contraseña utilizando password_verify
        if (password_verify($contrasenaAnterior, $contrasenaAlmacenada)) {
            // La contraseña anterior es correcta, ahora puedes proceder a cambiar otros campos
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];

            // Consulta para actualizar la información del usuario
            $sqlActualizarUsuario = "UPDATE usuarios SET nombre = '$nombre', correo = '$correo', telefono = '$telefono' WHERE id = $idUsuario";
            $resultActualizarUsuario = $conn->query($sqlActualizarUsuario);

            if ($resultActualizarUsuario) {
                $mensaje = "Información del usuario actualizada correctamente.";
            } else {
                $mensaje = "Error al actualizar la información del usuario: " . $conn->error;
            }
        } else {
            $mensaje = "La contraseña anterior es incorrecta.";
        }
    } else {
        $mensaje = "Error en la consulta de la contraseña almacenada: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Modificar Datos del Usuario</title>
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

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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

        .error {
            color: #ff0000;
            margin-top: 10px;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .popup button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
<script>
    function mostrarPopup(mensaje) {
        var popup = document.querySelector('.popup');
        var contenidoPopup = popup.querySelector('.contenido-popup');

        contenidoPopup.innerHTML = mensaje;
        popup.style.display = 'block';

        var botonAceptar = popup.querySelector('button');
        botonAceptar.addEventListener('click', function() {
            popup.style.display = 'none';
        });
    }
</script>



</head>
<body>
    <div class="container">
        <h2>Modificar Datos del Usuario</h2>

        <!-- Formulario de modificación de datos -->
        <form method="post" action="modificar_datos_usuario.php" onsubmit="mostrarPopup('<?php echo $mensaje; ?>');">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $datosUsuario['nombre']; ?>" required>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" value="<?php echo $datosUsuario['correo']; ?>" required>

            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" value="<?php echo $datosUsuario['telefono']; ?>" required>

            <label for="contrasena_anterior">Contraseña:</label>
            <input type="password" id="contrasena_anterior" name="contrasena_anterior" required>

            <input type="submit" value="Guardar Cambios">
        </form>

        <!-- Inicio -->
        <p><a href="index.php">Volver a la Página Principal</a></p>
    </div>

    <div class="popup">
        <div class="contenido-popup"></div>
        <p>Cambios guardados exitosamente!</p>
        <button>Aceptar</button>
    </div>
</body>
</html>