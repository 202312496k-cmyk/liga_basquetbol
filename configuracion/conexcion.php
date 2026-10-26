<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "liga_basquet";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>