<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_tarjeta = $_GET["id"];
$id_coleccion = $_GET["coleccion"];

// Eliminar tarjeta
$sql = "DELETE FROM tarjeta WHERE id_tarjeta = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_tarjeta);
$stmt->execute();

header("Location: coleccion.php?id=" . $id_coleccion);
exit();
?>
