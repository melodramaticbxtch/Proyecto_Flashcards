<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_coleccion = $_GET["coleccion"];

// Verificar que la colección pertenece al usuario
$sql = "SELECT * FROM coleccion WHERE id_coleccion = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_coleccion, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No tienes permiso para agregar tarjetas aquí.";
    exit();
}

// Si envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $termino = $_POST["termino"];
    $definicion = $_POST["definicion"];

    if (trim($termino) === "" || trim($definicion) === "") {
        echo "Completa ambos campos.";
    } else {
        $sqlInsert = "INSERT INTO tarjeta (id_coleccion, termino, definicion) VALUES (?, ?, ?)";
        $stmt2 = $conexion->prepare($sqlInsert);
        $stmt2->bind_param("iss", $id_coleccion, $termino, $definicion);

        if ($stmt2->execute()) {
            header("Location: coleccion.php?id=" . $id_coleccion);
            exit();
        } else {
            echo "Error al agregar tarjeta: " . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Nueva Tarjeta</title>
        <link rel="stylesheet" href="../css/nueva_tarjeta.css">
    </head>
    <body>
        <h1>Agregar tarjeta a: <?php echo $res->fetch_assoc()["nombre"]; ?></h1>

        <form method="POST">
            <label>Término</label>
            <input type="text" name="termino" required>

            <label>Definición</label>
            <textarea name="definicion" rows="4" required></textarea>

            <button type="submit">Guardar tarjeta</button>
        </form>

        <br>
        <a href="coleccion.php?id=<?php echo $id_coleccion; ?>">⬅ Volver</a>

    </body>
</html>
