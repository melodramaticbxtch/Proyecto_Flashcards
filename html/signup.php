<?php
include "../include/conexion.php";

echo "Conexión exitosa";
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
    </section>
  </div>
</body>
</html>
