<?php
include "../include/conexion.php"; // conexión lista

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = $_POST["usuario"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Preparar la consulta
    $sql = "INSERT INTO usuario (usuario, email, contraseña_hash) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $usuario, $email, $passwordHash);

    if ($stmt->execute()) {
        // Ir al login para que inicie sesión
        header("Location: login.php");
        exit();
    } else {
        echo "Error al registrar: " . $conexion->error;
    }
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="../css/login_singup.css">
</head>
<body>
  <div class="container">
    <section class="banner">
      <img src="../assets/signup.png" alt="Banner">
    </section>

    <!-- Formulario de registro -->
    <section class="login-section">
      <form class="login-form" method="POST" action="">
      <h2>Registrarse</h2>
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="email" name="email" placeholder="Correo Electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Crear cuenta</button>
    </form>


      </form>
    </section>
  </div>
</body>
</html>
