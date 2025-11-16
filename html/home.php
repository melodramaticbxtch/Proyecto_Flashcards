<?php
session_start();

// Evitar que entren sin iniciar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="../css/home.css">
  </head>
  <body>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
      <button class="close-btn" onclick="closeSidebar()">×</button>
      <a href="#">Inicio</a>
      <a href="#">Favoritos</a>
      <a href="#">Configuración</a>
      <a href="logout.php">Cerrar sesión</a> <!-- BOTÓN LOGOUT -->
    </aside>

    <div id="main">
      <header class="topbar">
        <button class="menu-btn" onclick="openSidebar()">☰</button>
        <h1 id="bienvenida">¡Bienvenid@, <?php echo $usuario; ?>!</h1>
      </header>

          <!-- Sección superior con cajitas -->
      <section class="plantillas" id="plantillas">
        <!-- Primera cajita fija para crear nueva colección -->
        <div class="plantilla nueva-coleccion">
          <span class="icon">➕</span>
          <a href="generar_sets.html">Crear nueva colección</a>
        </div>
        <!-- Cajitas de colecciones dinámicas se agregan con JS -->
      </section>


    <div class="crear-coleccion-boton">
      <button onclick="window.location.href='generar_sets.html'">+ Nueva Colección</button>
    </div>

    <section class="tableros">
      <h2>Tus Colecciones</h2>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Categoria</th>
            <th>Abierto por última vez</th>
          </tr>
        </thead>
        <tbody id="colecciones-body">
          <!-- Se llenará dinámicamente desde JS -->
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
