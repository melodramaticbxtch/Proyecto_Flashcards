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
    <style>
        .flashcard {
            width: 250px;
            height: 160px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 0 10px #0002;
            margin: 15px;
            position: relative;
            cursor: pointer;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .3s;
        }
        .flashcard.flipped {
            transform: rotateY(180deg);
        }
        .delete-btn {
            position:absolute;
            top:8px;
            right:10px;
            color:red;
            font-size:22px;
            text-decoration:none;
        }
    </style>
</head>

<body>

<h1><?php echo $coleccion["nombre"]; ?></h1>
<p><?php echo $coleccion["descripcion"]; ?></p>

<a href="eliminar_coleccion.php?id=<?php echo $id_coleccion; ?>"
   onclick="return confirm('¿Seguro que querés eliminar esta colección y todas sus tarjetas?');"
   style="color:red;">Eliminar colección completa</a>

<hr>
<a href="nueva_tarjeta.php?coleccion=<?php echo $id_coleccion; ?>" class="btn-agregar">
    + Agregar nueva tarjeta
</a>

<a href="home.php" class="btn-volver-home">⬅ Volver al Home</a>

<h2>Tarjetas</h2>

<div style="display:flex; flex-wrap:wrap;">
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

<script>
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
