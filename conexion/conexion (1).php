<?php
$host = "127.0.0.1";
$user = "root";
$pass = "Kiarabg18";       
$dbname = "flashcards_db";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
