<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET["id"]);
$fav = intval($_GET["fav"]);

$sql = "UPDATE coleccion SET favorita = ? WHERE id_coleccion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $fav, $id);
$stmt->execute();

header("Location: home.php");
exit();
