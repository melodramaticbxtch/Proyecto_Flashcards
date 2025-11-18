<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$usuario_nombre = $_SESSION["usuario"];

// Obtener solo colecciones favoritas
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM tarjeta t WHERE t.id_coleccion = c.id_coleccion) AS total_tarjetas
        FROM coleccion c
        WHERE c.id_usuario = ? AND c.favorita = 1
        ORDER BY c.creado_en DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$colecciones = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoritos</title>
    <link rel="stylesheet" href="../css/home.css">
  </head>
  <body>

      <!-- Sidebar -->
      <aside id="sidebar" class="sidebar">
        <button class="close-btn" onclick="closeSidebar()">×</button>
        <a href="home.php">Inicio</a>
        <a href="favoritos.php">Favoritos</a>
        <a href="perfil.php">Configuración</a>
        <a href="logout.php">Cerrar sesión</a>
      </aside>

      <!-- Contenido principal -->
      <div id="main">
        <header class="topbar">
          <button class="menu-btn" onclick="openSidebar()">☰</button>
          <h1>⭐ Favoritos de <?php echo htmlspecialchars($usuario_nombre); ?></h1>
        </header>

        <!-- Cajitas de colecciones favoritas -->
    <section class="plantillas" id="plantillas">

      <?php if ($colecciones->num_rows == 0) { ?>
          <p style="padding:20px; color:#777;">No tienes colecciones marcadas como favoritas.</p>
      <?php } ?>

      <?php while ($c = $colecciones->fetch_assoc()) { ?>
        <div class="plantilla dynamic"
            onclick="window.location.href='coleccion.php?id=<?php echo $c['id_coleccion']; ?>'">

          <span class="icon">📄</span>
          <p><?php echo htmlspecialchars($c['nombre']); ?></p>

          <!-- Estrella para quitar favorito -->
          <a 
              href="favorito.php?id=<?php echo $c['id_coleccion']; ?>&fav=0"
              onclick="event.stopPropagation();"
              style="float:right; font-size:20px; text-decoration:none;">
              ⭐
          </a>

        </div>
      <?php } ?>

    </section>

    <script>
    function openSidebar() {
      document.getElementById("sidebar").style.width = "250px";
      document.getElementById("main").style.marginLeft = "250px";
    }
    function closeSidebar() {
      document.getElementById("sidebar").style.width = "0";
      document.getElementById("main").style.marginLeft = "0";
    }
    </script>

  </body>
</html>
