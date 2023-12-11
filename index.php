<?php
session_start();
require_once 'config.php';

// Verifica si la sesión está iniciada y si existe la clave 'usuario' en $_SESSION
if (isset($_SESSION['usuario'])) {
    // Obtén los datos del usuario de la sesión
    $datosUsuario = $_SESSION['usuario'];

    // Consulta para obtener los datos del negocio
    $sqlNegocio = "SELECT * FROM negocio";
    $resultNegocio = $conn->query($sqlNegocio);

    // Resto del código que utilizará $datosUsuario y $resultNegocio
    // ...
} else {
    // Si la sesión no está iniciada o no existe la clave 'usuario'
    // No hagas nada o realiza alguna acción específica si lo deseas
    // Puedes dejar esta parte en blanco o agregar un mensaje, redirección, etc.
}


// Consulta para obtener los datos del negocio
$sql = "SELECT * FROM negocio";
$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Obtener la primera fila (asumiendo que solo hay un registro de negocio)
    $negocio = $result->fetch_assoc();
} else {
    // Puedes manejar el caso en el que no hay datos del negocio
    $negocio = null;
}
class Cancha {
    public $id;
    public $nombre;
    public $imagen;  // La misma imagen para todas las canchas

    public function __construct($id, $nombre, $imagen) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->imagen = $imagen;
    }
}

// Consulta a la base de datos para obtener las canchas
$sql = "SELECT * FROM canchas";
$result = $conn->query($sql);

// Verifica si hay resultados
if ($result->num_rows > 0) {
    $canchas = array();

    // Itera sobre los resultados y crea objetos Cancha
    while ($row = $result->fetch_assoc()) {
        // Asigna la misma imagen para todas las canchas
        $cancha = new Cancha($row['id'], $row['nombre'], 'canchasf5.png');
        $canchas[] = $cancha;
    }

?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='estilos.css'>
    <title>Canchas Disponibles</title>
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
        .container-top {
            max-width: 100%;
            margin: 0px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center; /* Centra el contenido dentro del contenedor */
        }

        h2 {
            color: #333;
        }

        .cancha {
            margin-bottom: 20px;
        }

        .cancha img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .cancha p {
            margin-top: 10px;
            text-align: center;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .mensaje {
            color: #ff0000; /* Color rojo para resaltar */
            margin-top: 20px;
            padding: 10px;
            background-color: #ffe5e5; /* Fondo rojo claro */
            border: 1px solid #ff9999; /* Borde rojo más oscuro */
            border-radius: 4px;
            display: inline-block; /* Alinea el mensaje a la derecha y permite que parpadee */
            animation: blink 1s infinite; /* Animación de parpadeo */
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }
.nombre-negocio {
    font-size: 54px;
    animation: colorChange 4s infinite; /* Cambio de color durante 2 segundos, infinitamente repetido */
    margin: auto;
    width: fit-content;
}

@keyframes colorChange {
    0% {
        color: red; /* Color inicial */
    }
    50% {
        color: blue; /* Cambio de color a la mitad de la animación */
    }
    100% {
        color: red; /* Vuelve al color inicial al final de la animación */
    }
}

        .datos-negocio {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .datos-negocio p {
            margin-right: 10px;
        }


.menu-no-sesion {
    float: right;
    margin-top: 50px;
    margin-right: 260px; /* Margen solo en el lado derecho */
    position: relative;
}

.menu-no-sesion a {
    margin: 0 10px; /* Espaciado entre los enlaces */
    color: #333;
    text-decoration: none;
    font-weight: bold;
}

.menu-no-sesion a:hover {
    text-decoration: underline;
}

.usuario-info {
    float: right;
    margin-top: 10px;
    position: relative;
}

.menu-desplegable {
    display: none;
    position: absolute;
    top: 25px; /* Ajusta la distancia desde la parte superior */
    right: 10px; /* Ajusta la distancia desde la derecha */
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

.usuario-info p {
    margin: 0;
    cursor: pointer;
}
    </style>
</head>
<body>
    <?php if (isset($_SESSION['usuario'])) : ?>
        <!-- Menú para usuarios con sesión iniciada -->
        <div class="usuario-info hidden" id="menuUsuario">
            <p onclick="toggleMenu()">Bienvenido, <strong><?php echo $datosUsuario['nombre']; ?></strong> &#9660;</p>
            <div class="menu-desplegable" id="menuDesplegable">
                <a href="admin/index.php">Administrar</a>
                <a href="modificar_datos_usuario.php">Usuario</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    <?php else : ?>
        <!-- Menú para usuarios sin sesión iniciada -->
        <div class="menu-no-sesion">
            <a href="login.php">Iniciar Sesión</a>
            <span> o </span>
            <a href="registrar.php">Registrarse</a>
        </div>
    <?php endif; ?>
        <!-- Mostrar otros datos del usuario si es necesario -->
<p class="nombre-negocio"><strong><?php echo $negocio['nombre']; ?></strong></p>


        <!-- Agrega más contenido según tus necesidades -->

    </div>

    <div class='container'> <!-- Contenedor para centrar todo el contenido -->
<br>
        <h2>Canchas Disponibles:</h2>

        <?php foreach ($canchas as $cancha) : ?>
            <div class='cancha'>
                <a href='reservar.php?id=<?= $cancha->id ?>'>
                    <img src='<?= $cancha->imagen ?>' alt='<?= $cancha->nombre ?>'>
                    <p><?= $cancha->nombre ?></p>
                </a>
            </div>
        <?php endforeach; ?>
<br>
<div>
<?php if ($negocio !== null) : ?>
            <div class="datos-negocio">
                <p>Dirección: <strong><?php echo $negocio['direccion']; ?></strong></p>
                <p>- Teléfono: <strong><?php echo $negocio['telefono']; ?></strong></p>
                <p>- WhatsApp: <strong><?php echo $negocio['whatsapp']; ?></strong></p>
                <p>- Localidad: <strong><?php echo $negocio['localidad']; ?></strong></p>
            </div>
        <?php else : ?>
            <p>No hay datos de negocio disponibles en este momento.</p>
        <?php endif; ?>
</div>
        </div>
        <div>
        <p class='mensaje'>Debes Iniciar Sesión para Reservar</p>
        </div>
        <?php
        session_start();
        if (isset($_SESSION['usuario'])) :
        ?>
  

        <?php endif; ?>

    </div> <!-- Fin del contenedor -->
 

    <script>
        function toggleMenu() {
            var menuDesplegable = document.getElementById("menuDesplegable");
            menuDesplegable.style.display = (menuDesplegable.style.display === "block") ? "none" : "block";
        }
    </script>
    </body>
</html>

<?php
} else {
    echo "<p>No hay canchas disponibles en este momento.</p>";
}
?>