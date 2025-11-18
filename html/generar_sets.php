<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Cuando se envía el formulario completo
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $categoria = $_POST["categoria"];

    // Crear colección
    $sqlInsert = "INSERT INTO coleccion (id_usuario, nombre, categoria, descripcion) 
                  VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sqlInsert);
    $stmt->bind_param("isss", $id_usuario, $nombre, $categoria, $descripcion);

    if ($stmt->execute()) {
        $id_coleccion = $stmt->insert_id;

        // Insertar tarjetas
        if (isset($_POST["termino"])) {
            for ($i = 0; $i < count($_POST["termino"]); $i++) {
                $term = trim($_POST["termino"][$i]);
                $def = trim($_POST["definicion"][$i]);

                if ($term !== "" && $def !== "") {
                    $sqlCard = "INSERT INTO tarjeta (id_coleccion, termino, definicion)
                                VALUES (?, ?, ?)";
                    $stmtCard = $conexion->prepare($sqlCard);
                    $stmtCard->bind_param("iss", $id_coleccion, $term, $def);
                    $stmtCard->execute();
                }
            }
        }

        header("Location: home.php");
        exit();

    } else {
        echo "Error creando colección: " . $conexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Crear nuevo set</title>
        <link rel="stylesheet" href="../css/generador_sets.css">
    </head>

    <body>

            <div class="container">

                <h1>Crear nuevo set</h1>

                <form method="POST" id="formSet">

                    <div class="set-info">
                        <input type="text" name="nombre" id="setName" placeholder="Nombre del set" required>

                        <textarea name="descripcion" id="setDescription" rows="3"
                            placeholder="Descripción del set (opcional)"></textarea>

                        <input type="text" name="categoria" id="categoria"
                            placeholder="Categoría (ej: Matemática, Inglés, Historia)">
                    </div>

                    <h2>Tarjetas</h2>

                    <div id="cards-container" class="cards"></div>

                    <div class="actions">
                        <button type="button" class="add-card" onclick="agregarTarjeta()">Agregar Tarjeta</button>
                        <button type="submit" class="save">Guardar Set</button>
                    </div>

                </form>
            </div>

            <script>
                function openSidebar() {
                document.getElementById("sidebar").style.width = "250px";
                document.getElementById("main").style.marginLeft = "250px";
                }
                function closeSidebar() {
                document.getElementById("sidebar").style.width = "0";
                document.getElementById("main").style.marginLeft = "0";
                }
                const container = document.getElementById("cards-container");

                // Crear la primera tarjeta por defecto
                agregarTarjeta();

                function agregarTarjeta() {
                    const card = document.createElement("div");
                    card.classList.add("card");

                    card.innerHTML = `
                        <input type="text" name="termino[]" placeholder="Término">
                        <input type="text" name="definicion[]" placeholder="Definición">
                        <button type="button" class="delete-btn" onclick="this.parentElement.remove()">Eliminar</button>
                    `;

                    container.appendChild(card);
                }
            </script>

    </body>
</html>
