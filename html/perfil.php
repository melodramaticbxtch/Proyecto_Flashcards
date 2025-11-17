<?php
session_start();
include "../include/conexion.php";

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";

// Cambiar email
if (isset($_POST['cambiar_email'])) {
    $nuevo_email = trim($_POST['email']);
    if (filter_var($nuevo_email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conexion->prepare("UPDATE usuario SET email = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nuevo_email, $id_usuario);
        if ($stmt->execute()) {
            $mensaje = "Email actualizado correctamente.";
        } else {
            $mensaje = "Error al actualizar el email.";
        }
        $stmt->close();
    } else {
        $mensaje = "Email inválido.";
    }
}

// Cambiar contraseña
if (isset($_POST['cambiar_contraseña'])) {
    $pass_actual = $_POST['pass_actual'];
    $nueva_pass = $_POST['nueva_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    $stmt = $conexion->prepare("SELECT contraseña_hash FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($hash_actual);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($pass_actual, $hash_actual)) {
        if ($nueva_pass === $confirm_pass) {
            $nuevo_hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuario SET contraseña_hash = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $nuevo_hash, $id_usuario);
            if ($stmt->execute()) {
                $mensaje = "Contraseña actualizada correctamente.";
            } else {
                $mensaje = "Error al actualizar la contraseña.";
            }
            $stmt->close();
        } else {
            $mensaje = "Las nuevas contraseñas no coinciden.";
        }
    } else {
        $mensaje = "La contraseña actual es incorrecta.";
    }
}

// Eliminar cuenta
if (isset($_POST['eliminar_cuenta'])) {
    $stmt = $conexion->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    if ($stmt->execute()) {
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        $mensaje = "Error al eliminar la cuenta.";
    }
}

// Traer datos actuales
$stmt = $conexion->prepare("SELECT usuario, email FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($usuario_nombre, $email_actual);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración de Perfil</title>
<link rel="stylesheet" href="../css/perfil.css">
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
<h1>Configuración de Perfil</h1>

<?php if ($mensaje) echo "<p>$mensaje</p>"; ?>

<form method="POST">
    <h2>Cambiar Email</h2>
    <input type="email" name="email" value="<?= htmlspecialchars($email_actual) ?>" required>
    <button type="submit" name="cambiar_email">Actualizar Email</button>
</form>

<form method="POST">
    <h2>Cambiar Contraseña</h2>
    <input type="password" name="pass_actual" placeholder="Contraseña actual" required><br>
    <input type="password" name="nueva_pass" placeholder="Nueva contraseña" required><br>
    <input type="password" name="confirm_pass" placeholder="Confirmar nueva contraseña" required><br>
    <button type="submit" name="cambiar_contraseña">Actualizar Contraseña</button>
</form>

<form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar tu cuenta?');">
    <h2>Eliminar Cuenta</h2>
    <button type="submit" name="eliminar_cuenta">Eliminar Cuenta</button>
</form>
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
