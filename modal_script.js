function abrirModal(mensaje, esError = false) {
    var modal = document.getElementById("myModal");
    var mensajeModal = document.getElementById("mensajeModal");

    mensajeModal.innerHTML = mensaje;

    if (esError) {
        mensajeModal.style.color = "#721c24";
        mensajeModal.style.backgroundColor = "#f8d7da";
        mensajeModal.style.border = "1px solid #f5c6cb";
    } else {
        mensajeModal.style.color = "#155724";
        mensajeModal.style.backgroundColor = "#d4edda";
        mensajeModal.style.border = "1px solid #c3e6cb";
    }

    modal.style.display = "block";
}

function cerrarModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}

// Cierra el modal después de 3 segundos (puedes ajustar este tiempo según tus preferencias)
setTimeout(cerrarModal, 3000);