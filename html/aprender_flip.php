<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_coleccion = intval($_GET["id"]);

// Traer tarjetas de la colección
$sql = "SELECT * FROM tarjeta WHERE id_coleccion = ? ORDER BY RAND()";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_coleccion);
$stmt->execute();
$result = $stmt->get_result();

$tarjetas = [];
while ($row = $result->fetch_assoc()) {
    $tarjetas[] = $row;
}

// Si no hay tarjetas
if (count($tarjetas) == 0) {
    echo "<script>alert('Esta colección no tiene tarjetas'); window.location.href='coleccion.php?id=$id_coleccion';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Modo Aprender</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/aprender_flip.css">
  </head>
  <body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
      <button class="close-btn" onclick="closeSidebar()">×</button>
      <a href="home.php">Inicio</a>
      <a href="favoritos_pag.php">Favoritos</a>
      <a href="perfil.php">Configuración</a>
      <a href="logout.php">Cerrar sesión</a>
    </aside>

    <!-- Top Bar -->
    <div id="main">
      <header class="topbar">
        <button class="menu-btn" onclick="openSidebar()">☰</button>
        <h1 id="bienvenida">¡Hora de aprender!</h1>
      </header>
    </div>
    <div class="learn-container">

      <h2>Aprendiendo colección</h2>

      <!-- Progreso -->
      <div class="progress-bar">
        <div class="progress-fill" id="progress"></div>
      </div>

      <!-- Tarjeta -->
      <div class="flip-card" id="flipCard">
        <div class="flip-inner">
          <div class="flip-front" id="cardFront"></div>
          <div class="flip-back" id="cardBack"></div>
        </div>
      </div>

      <!-- Botones -->
      <div class="learn-buttons">
        <button onclick="siguiente()">Siguiente</button>
        <button onclick="meLaSe()">Me la sé ✔</button>
        <button onclick="noMeLaSe()">No me la sé ❌</button>
      </div>
  |</div>

    <script>
      function openSidebar() {
      document.getElementById("sidebar").style.width = "250px";
      document.getElementById("main").style.marginLeft = "250px";
      }
      function closeSidebar() {
        document.getElementById("sidebar").style.width = "0";
        document.getElementById("main").style.marginLeft = "0";
      }
      const tarjetas = <?php echo json_encode($tarjetas); ?>;

      let index = 0;
      let correctas = 0;

      // mostrar tarjeta inicial
      cargarTarjeta();

      // click para flip
      document.getElementById("flipCard").addEventListener("click", () => {
        document.getElementById("flipCard").classList.toggle("flipped");
      });

      // cargar tarjeta actual
      function cargarTarjeta() {
        const t = tarjetas[index];
        document.getElementById("flipCard").classList.remove("flipped");
        document.getElementById("cardFront").innerText = t.termino;
        document.getElementById("cardBack").innerText = t.definicion;

        let progreso = ((index) / tarjetas.length) * 100;
        document.getElementById("progress").style.width = progreso + "%";
      }

      function siguiente() {
        index++;
        if (index >= tarjetas.length) return finalizar();
        cargarTarjeta();
      }

      function meLaSe() {
        correctas++;
        siguiente();
      }

      function noMeLaSe() {
        siguiente();
      }
      function finalizar() {
        document.querySelector(".learn-container").innerHTML =
          `<h2>¡Completado!</h2>
          <p>Acertaste ${correctas} de ${tarjetas.length} tarjetas.</p>
          <a href="coleccion.php?id=<?php echo $id_coleccion; ?>" class="btn-volver">
              Volver a la colección
          </a>`;
        }

    </script>

  </body>
</html>
