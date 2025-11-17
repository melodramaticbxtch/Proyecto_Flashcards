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
<link rel="stylesheet" href="../css/home.css">

<style>
.quiz-container {
  max-width: 600px;
  margin: auto;
  padding: 20px;
  text-align: center;
}
.progress-bar {
  width: 100%;
  background: #ddd;
  border-radius: 20px;
  margin-bottom: 20px;
  height: 12px;
}
.progress-fill {
  width: 0%;
  height: 100%;
  background: #57a1ff;
  border-radius: 20px;
  transition: 0.3s;
}
.question {
  font-size: 22px;
  margin-bottom: 25px;
  font-weight: bold;
}
.options button {
  display: block;
  width: 100%;
  margin: 10px 0;
  padding: 12px;
  font-size: 18px;
  border-radius: 8px;
  cursor: pointer;
  border: 1px solid #444;
}
.correct {
  background: #4caf50 !important;
  color: white;
}
.wrong {
  background: #e74c3c !important;
  color: white;
}
</style>
</head>
<body>

<div class="quiz-container">
  <h2>Modo Quiz</h2>

  <div class="progress-bar">
    <div class="progress-fill" id="progress"></div>
  </div>

  <div class="question" id="questionText"></div>

  <div class="options" id="optionsContainer"></div>

</div>

<script>
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
      <button onclick="window.location.href='coleccion.php?id=<?php echo $id_coleccion; ?>'">
        Volver a la colección
      </button>
    `;
}
</script>

</body>
</html>
