<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_coleccion = $_GET["id"];

// Verificar que la colección pertenece al usuario
$sql = "SELECT * FROM coleccion WHERE id_coleccion = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_coleccion, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No tienes permiso para eliminar esta colección.";
    exit();
}

// Eliminar colección (las tarjetas se borran por CASCADE)
$sqlDelete = "DELETE FROM coleccion WHERE id_coleccion = ?";
$stmt2 = $conexion->prepare($sqlDelete);
$stmt2->bind_param("i", $id_coleccion);
$stmt2->execute();

header("Location: home.php");
exit();
?>
