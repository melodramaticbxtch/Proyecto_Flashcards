<?php
session_start();
include "../include/conexion.php";

$id_usuario = $_SESSION["id_usuario"];
$id_coleccion = $_POST["id_coleccion"];
$termino = $_POST["termino"];
$def = $_POST["definicion"];

$sql = "INSERT INTO tarjeta (id_coleccion, termino, definicion) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iss", $id_coleccion, $termino, $def);
$stmt->execute();

header("Location: coleccion.php?id=" . $id_coleccion);
exit();
?>
