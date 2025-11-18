<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$usuario_nombre = $_SESSION["usuario"];

// Obtener colecciones ordenadas (favoritas primero)
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM tarjeta t WHERE t.id_coleccion = c.id_coleccion) AS total_tarjetas
        FROM coleccion c
        WHERE c.id_usuario = ?
        ORDER BY c.favorita DESC, c.creado_en DESC";

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
    <title>Home</title>
    <link rel="stylesheet" href="../css/home.css">
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

      <!-- Contenido principal -->
      <div id="main">
        <header class="topbar">
          <button class="menu-btn" onclick="openSidebar()">☰</button>
          <h1 id="bienvenida">¡Bienvenid@, <?php echo htmlspecialchars($usuario_nombre); ?>!</h1>
        </header>

        <!-- Sección superior con cajitas -->
    <section class="plantillas" id="plantillas">

      <!-- Primera cajita fija para crear nueva colección -->
      <div class="plantilla nueva-coleccion">
        <span class="icon">➕</span>
        <a href="generar_sets.php">Crear nueva colección</a>
      </div>

      <!-- Cajitas de colecciones desde la base -->
      <?php while ($c = $colecciones->fetch_assoc()) { ?>
        <div class="plantilla dynamic"
            onclick="window.location.href='coleccion.php?id=<?php echo $c['id_coleccion']; ?>'">
          
          <span class="icon">📄</span>
          <p><?php echo htmlspecialchars($c['nombre']); ?></p>

          <!-- Botón de favorito -->
          <a href="favorito.php?id=<?php echo $c['id_coleccion']; ?>&fav=<?php echo ($c['favorita'] ? 0 : 1); ?>"
            onclick="event.stopPropagation();"
            style="float:right; font-size:20px; text-decoration:none;">
            <?php echo $c['favorita'] ? "⭐" : "☆"; ?>
          </a>

        </div>
      <?php } ?>

    </section>

    <!-- Botón extra para crear colección -->
    <div class="crear-coleccion-boton">
      <button onclick="window.location.href='generar_sets.php'">+ Nueva Colección</button>
    </div>

    <!-- Sección inferior (tabla) -->
    <section class="tableros">
      <h2>Tus Colecciones</h2>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Tarjetas</th>
            <th>Creado</th>
            <th>Último acceso</th>
          </tr>
        </thead>
        <tbody>

          <?php
          // Reiniciar puntero del resultset
          $stmt->execute();
          $colecciones = $stmt->get_result();

          if ($colecciones->num_rows == 0) { ?>
            <tr>
              <td colspan="5" style="text-align:center; color: #888;">No tienes colecciones aún</td>
            </tr>
          <?php 
          } else {
            while ($c = $colecciones->fetch_assoc()) { ?>
              <tr onclick="window.location.href='coleccion.php?id=<?php echo $c['id_coleccion']; ?>'"
                  style="cursor:pointer;">

                <td>
                  <strong><?php echo htmlspecialchars($c["nombre"]); ?></strong>

                  <!-- Botón de favorito -->
                  <a href="favorito.php?id=<?php echo $c['id_coleccion']; ?>&fav=<?php echo ($c['favorita'] ? 0 : 1); ?>"
                    onclick="event.stopPropagation();"
                    style="margin-left:8px; text-decoration:none;">
                    <?php echo $c['favorita'] ? "⭐" : "☆"; ?>
                  </a>
                </td>

                <td><?php echo $c["categoria"] ?: "-"; ?></td>
                <td><?php echo $c["total_tarjetas"]; ?></td>
                <td><?php echo date("d/m/Y", strtotime($c["creado_en"])); ?></td>
                <td><?php echo $c["ultimo_acceso"] ? date("d/m/Y", strtotime($c["ultimo_acceso"])) : "-"; ?></td>

              </tr>
          <?php 
            }
          }
          ?>

        </tbody>
      </table>
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
