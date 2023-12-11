<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) {
    // Lógica para eliminar usuarios
    $usuarioIdAEliminar = $_POST['eliminar_usuario'];

    // Consulta preparada para evitar inyección de SQL
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("s", $usuarioIdAEliminar);

    if ($stmt->execute()) {
        $mensaje = "Usuario eliminado con éxito.";

        // Agregar el código HTML y JavaScript para mostrar el modal de éxito
        echo '<!DOCTYPE html>
            <html lang="es">
            <head>
                <!-- Agrega aquí tus etiquetas meta y enlaces de estilo -->
            </head>
            <body>
                <script>
                    // Muestra el modal de éxito al cargar la página
                    document.addEventListener("DOMContentLoaded", function() {
                        mostrarModalExitoEliminarUsuario("'.$mensaje.'");
                    });
                </script>';
    } else {
        $mensaje = "Error al eliminar el usuario: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Redirigir a la página principal si se intenta acceder directamente a este archivo
    header('Location: index.php');
    exit();
}

// Redirigir de nuevo a la página principal con el mensaje
echo '      <script>
                    // Redirige después de mostrar el modal (puedes ajustar el tiempo según tus necesidades)
                    setTimeout(function() {
                        window.location.href = "usuarios.php?mensaje=' . urlencode($mensaje) . '";
                    }, 2000);
                </script>
            </body>
        </html>';
exit();
?>