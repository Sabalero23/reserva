<?php
require_once 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar_horario'])) {
    // Validar y escapar los datos del formulario
    $cancha_id = $_POST['cancha_id'];
    $horario_id = $_POST['horario_id'];

    // Verificar si el horario ya está reservado
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE cancha_id = ? AND horario_id = ?");
    $stmt->bind_param("ii", $cancha_id, $horario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Realizar la reserva
        $usuario_id = $_SESSION['usuario']['id'];
        $stmt = $conn->prepare("INSERT INTO reservas (cancha_id, horario_id, usuario_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cancha_id, $horario_id, $usuario_id);

        if ($stmt->execute()) {
            $mensaje = "Reserva realizada con éxito.";
        } else {
            $mensaje = "Error al realizar la reserva: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "El horario seleccionado ya está reservado.";
    }

    // Redireccionar a la página de reserva con el mensaje
    header("Location: reservar.php?id=$cancha_id&mensaje=$mensaje");
    exit();
} else {
    // Redireccionar si no se envió correctamente el formulario
    header('Location: index.php');
    exit();
}

$conn->close();
?>