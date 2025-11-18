<?php
session_start();
include "../include/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_coleccion = intval($_GET["id"]);

// Traer tarjetas
$sql = "SELECT * FROM tarjeta WHERE id_coleccion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_coleccion);
$stmt->execute();
$result = $stmt->get_result();

$tarjetas = [];
while ($row = $result->fetch_assoc()) {
    $tarjetas[] = $row;
}

if (count($tarjetas) < 4) {
    echo "<script>alert('Necesitas al menos 4 tarjetas para hacer un quiz.'); window.location.href='coleccion.php?id=$id_coleccion';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Modo Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/aprender_quiz.css">
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

    <div class="quiz-container">
      <h2>Modo Quiz</h2>

      <div class="progress-bar">
        <div class="progress-fill" id="progress"></div>
      </div>

      <div class="question" id="questionText"></div>

      <div class="options" id="optionsContainer"></div>

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
      // Tarjetas desde PHP
      const tarjetas = <?php echo json_encode($tarjetas); ?>;

      // Se mezclan
      tarjetas.sort(() => Math.random() - 0.5);

      let index = 0;
      let correctas = 0;

      mostrarPregunta();

      function mostrarPregunta() {
          const t = tarjetas[index];

          document.getElementById("questionText").innerText =
            "¿Qué término corresponde a esta definición?\n\n" + t.definicion;

          // Crear opciones (1 correcta + 3 incorrectas)
          let opciones = [t.termino];

          // Agregar incorrectas aleatorias
          let distractores = tarjetas
              .filter(x => x.id_tarjeta !== t.id_tarjeta)
              .sort(() => Math.random() - 0.5)
              .slice(0, 3)
              .map(x => x.termino);

          opciones = opciones.concat(distractores);

          // Mezclar opciones
          opciones.sort(() => Math.random() - 0.5);

          const cont = document.getElementById("optionsContainer");
          cont.innerHTML = "";

          opciones.forEach(op => {
              let btn = document.createElement("button");
              btn.innerText = op;

              btn.onclick = () => verificar(op, t.termino, btn);

              cont.appendChild(btn);
          });

          let progreso = (index / tarjetas.length) * 100;
          document.getElementById("progress").style.width = progreso + "%";
      }

      function verificar(opcion, correcta, boton) {
          const botones = document.querySelectorAll(".options button");

          if (opcion === correcta) {
              boton.classList.add("correct");
              correctas++;
          } else {
              boton.classList.add("wrong");
              // marcar la correcta
              botones.forEach(b => {
                if (b.innerText === correcta) b.classList.add("correct");
              });
          }

          // bloquear
          botones.forEach(b => b.disabled = true);

          // siguiente
          setTimeout(() => {
              index++;
              if (index >= tarjetas.length) finalizarQuiz();
              else mostrarPregunta();
          }, 1200);
      }
      function finalizarQuiz() {
        document.querySelector(".quiz-container").innerHTML =
          `
            <h2>¡Quiz finalizado!</h2>
            <p>Respuestas correctas: <strong>${correctas}</strong> de ${tarjetas.length}</p>
            <a href="coleccion.php?id=<?php echo $id_coleccion; ?>" class="btn-volver">
              Volver a la colección
            </a>
          `;
      }

    </script>

  </body>
</html>
