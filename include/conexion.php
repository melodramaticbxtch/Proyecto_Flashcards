<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "flashcards_db";
$port = 3307; // IMPORTANTE!

$conexion = new mysqli($server, $user, $pass, $db, $port);

if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
