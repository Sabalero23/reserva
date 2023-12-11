<?php
$servername = "localhost";
$username = "c1782212_reser";
$password = "zuZAtoru58";
$dbname = "c1782212_reser";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>