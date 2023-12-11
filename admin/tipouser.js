document.addEventListener('DOMContentLoaded', function () {
  <?php if ($_SESSION['usuario']['rol'] === 'usuario') : ?>
    var confirmacion = confirm('No tienes permisos suficientes. ¿Quieres volver al inicio?');
    if (confirmacion) {
      window.location.href = '../index.php';
    }
  <?php endif; ?>
});