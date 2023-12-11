<?php
require_once '../config.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: login.php'); // Redirige a la página de inicio de sesión si no es un administrador
    exit();
}

// Lógica para crear usuarios y asignar roles
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar_usuario'])) {
        // Lógica para eliminar usuarios
        $usuarioIdAEliminar = $_POST['eliminar_usuario'];

        // Consulta preparada para evitar inyección de SQL
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("s", $usuarioIdAEliminar);

        if ($stmt->execute()) {
            $mensaje = "Usuario eliminado con éxito.";
        } else {
            $mensaje = "Error al eliminar el usuario: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['contrasena'], $_POST['confirmar_contrasena'], $_POST['rol'])) {
        // Lógica para crear usuarios
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $contrasena = $_POST['contrasena'];
        $confirmar_contrasena = $_POST['confirmar_contrasena'];
        $rol = $_POST['rol'];

        // Validar y escapar los datos
        $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);
        $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
        $telefono = filter_var($telefono, FILTER_SANITIZE_STRING);

        // Verificar si las contraseñas coinciden
        if ($contrasena !== $confirmar_contrasena) {
            $mensaje = "Las contraseñas no coinciden. Por favor, inténtelo de nuevo.";
        } else {
            // Hash de la contraseña utilizando password_hash
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Consulta preparada para evitar inyección de SQL
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, telefono, contrasena, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre, $correo, $telefono, $contrasena_hash, $rol);

            if ($stmt->execute()) {
                $mensaje = "Usuario creado con éxito.";
            } else {
                $mensaje = "Error al crear el usuario: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
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
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
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
            background-color: #007bff;
            width: 200px;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            color: #555;
            margin-top: 20px;
            text-align: center;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
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
        <br>
        <p><a href="index.php">Volver a la Página Principal</a></p>
        <br>
        <h2>Administración de Usuarios</h2>

        <?php
        if (isset($mensaje)) {
            echo "<p>{$mensaje}</p>";
        }
        ?>

        <!-- Formulario para crear usuarios -->
        <form method="post" action="usuarios.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="correo">Correo:</label>
            <input type="text" id="correo" name="correo" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label for="confirmar_contrasena">Confirmar Contraseña:</label>
            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>

            <label for="rol">Rol:</label>
            <select name="rol">
                <option value="usuario">Usuario</option>
                <option value="admin">Administrador</option>
            </select>

            <button type="submit">Crear Usuario</button>
        </form>

        <!-- Lista de usuarios existentes -->
        <h3>Usuarios Actuales:</h3>
        <?php
        $result = $conn->query("SELECT * FROM usuarios");
        $usuarios = $result->fetch_all(MYSQLI_ASSOC);

        if ($result->num_rows > 0) {
            echo "<ul>";
            foreach ($usuarios as $usuario) {
                echo "<li>{$usuario['nombre']} ({$usuario['correo']}, {$usuario['telefono']}) - Rol: {$usuario['rol']} ";
                echo "<a href='#' onclick=\"confirmarEliminarUsuario('{$usuario['id']}', '{$usuario['nombre']}')\">Eliminar</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No hay usuarios registrados.</p>";
        }
        ?>

    </div>

    <!-- Modal de confirmación para eliminar usuario -->
    <div id="modalEliminarUsuario" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEliminarUsuario()">&times;</span>
            <p id="confirmacionMensaje"></p>
            <button onclick="eliminarUsuario()">Eliminar</button>
        </div>
    </div>

    <!-- Modal de éxito al eliminar usuario -->
    <div id="modalExitoEliminarUsuario" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalExitoEliminarUsuario()">&times;</span>
            <p id="exitoEliminarUsuarioMensaje"></p>
        </div>
    </div>

    <!-- Agregar esto en la sección de scripts JavaScript -->
    <script>
        var usuarioIdAEliminar = null;

        function confirmarEliminarUsuario(usuarioId, nombreUsuario) {
            // Almacena el ID del usuario a eliminar y muestra el modal
            usuarioIdAEliminar = usuarioId;
            document.getElementById('confirmacionMensaje').innerHTML = `¿Está seguro de que desea eliminar al usuario ${nombreUsuario}?`;
            document.getElementById('modalEliminarUsuario').style.display = 'block';
        }

        function cerrarModalEliminarUsuario() {
            // Cierra el modal y reinicia la variable global
            document.getElementById('modalEliminarUsuario').style.display = 'none';
            usuarioIdAEliminar = null;
        }

        function eliminarUsuario() {
            // Envía el formulario para eliminar el usuario
            document.getElementById('usuario_id_a_eliminar').value = usuarioIdAEliminar;
            document.getElementById('formEliminarUsuario').submit();
        }

        function cerrarModalExitoEliminarUsuario() {
            // Cierra el modal de éxito al eliminar usuario
            document.getElementById('modalExitoEliminarUsuario').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>