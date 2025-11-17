<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_coleccion = $_GET["id"];

// ============================================================
// 1. Verificar que la colección existe y pertenece al usuario
// ============================================================
$sql = "SELECT * FROM coleccion WHERE id_coleccion = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en prepare(): " . $conexion->error);
}

$stmt->bind_param("ii", $id_coleccion, $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "No puedes acceder a esta colección.";
    exit();
}

$coleccion = $resultado->fetch_assoc();

// ============================================================
// 2. Registrar la fecha último acceso
// ============================================================
$update = $conexion->prepare("UPDATE coleccion SET ultimo_acceso = NOW() WHERE id_coleccion = ?");
$update->bind_param("i", $id_coleccion);
$update->execute();

// ============================================================
// 3. Obtener tarjetas de esta colección
// ============================================================
$sqlTarjetas = "SELECT * FROM tarjeta WHERE id_coleccion = ?";
$stmtTarjetas = $conexion->prepare($sqlTarjetas);

if (!$stmtTarjetas) {
    die("Error en prepare() tarjetas: " . $conexion->error);
}

$stmtTarjetas->bind_param("i", $id_coleccion);
$stmtTarjetas->execute();
$tarjetas = $stmtTarjetas->get_result();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $coleccion["nombre"]; ?></title>
    <link rel="stylesheet" href="../css/coleccion.css">
</head>

<body>
  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar">
    <button class="close-btn" onclick="closeSidebar()">×</button>
    <a href="home.php">Inicio</a>
    <a href="favoritos_pag.php">Favoritos</a>
    <a href="#">Configuración</a>
    <a href="logout.php">Cerrar sesión</a>
  </aside>

  <!-- Contenido principal -->
  <div id="main">
    <header class="topbar">
      <button class="menu-btn" onclick="openSidebar()">☰</button>
      <h1 id="bienvenida">Colección "<?php echo htmlspecialchars($coleccion["nombre"]); ?>"</h1>
    </header>

    <!-- Caja de descripción -->
    <h3 class="subtitulo-desc">Descripción</h3>
    <p class="descripcion"><?php echo $coleccion["descripcion"]; ?></p>


    <!-- Acciones -->
    <div class="acciones">
      <a href="nueva_tarjeta.php?coleccion=<?php echo $id_coleccion; ?>" class="btn-agregar">
          + Agregar nueva tarjeta
      </a>

      <button onclick="window.location.href='aprender_flip.php?id=<?php echo $id_coleccion; ?>'">
        Aprender (Flip)
      </button>

      <button onclick="window.location.href='aprender_quiz.php?id=<?php echo $id_coleccion; ?>'">
        Quiz (Multiple Choice)
      </button>
    </div>

    <h2>Tarjetas</h2>

    <div class="lista-tarjetas">
      <?php while($t = $tarjetas->fetch_assoc()): ?>
        <div class="flashcard" onclick="flipCard(this)">
            <div class="front">
                <?php echo htmlspecialchars($t["termino"]); ?>
            </div>

            <div class="back" style="display:none;">
                <?php echo htmlspecialchars($t["definicion"]); ?>
            </div>

            <a class="delete-btn"
               href="eliminar_tarjeta.php?id=<?php echo $t['id_tarjeta']; ?>&coleccion=<?php echo $id_coleccion; ?>"
               onclick="return confirm('¿Eliminar tarjeta?');">×</a>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Botón grande rojo al final -->
    <div class="eliminar-container">
      <a class="btn-eliminar"
         href="eliminar_coleccion.php?id=<?php echo $id_coleccion; ?>"
         onclick="return confirm('¿Seguro que querés eliminar esta colección y todas sus tarjetas?');">
         Eliminar colección completa
      </a>
    </div>

  </div>

  <!-- Scripts -->
  <script>
  function openSidebar() {
      document.getElementById("sidebar").style.width = "250px";
      document.getElementById("main").style.marginLeft = "250px";
  }
  function closeSidebar() {
      document.getElementById("sidebar").style.width = "0";
      document.getElementById("main").style.marginLeft = "0";
  }

  function flipCard(card) {
      const front = card.querySelector(".front");
      const back = card.querySelector(".back");

      const isFlipped = back.style.display === "block";

      if (isFlipped) {
          back.style.display = "none";
          front.style.display = "block";
      } else {
          back.style.display = "block";
          front.style.display = "none";
      }
  }
  </script>

</body>
</html>
