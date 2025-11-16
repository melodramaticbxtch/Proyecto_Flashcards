<?php
include "../include/conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Buscar usuario
    $sql = "SELECT * FROM usuario WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $fila = $resultado->fetch_assoc();

        // Verificar contraseña (usa contraseña_hash)
        if (password_verify($password, $fila['contraseña_hash'])) {

            // Guardar sesión
            $_SESSION['usuario']   = $fila['usuario'];
            $_SESSION['id_usuario'] = $fila['id_usuario'];

            // Ir al home
            header("Location: home.php");
            exit();

        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href='login.php';</script>";
        }

    } else {
        echo "<script>alert('Usuario no encontrado'); window.location.href='login.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login_singup.css">
</head>
<body>
    <div class="container">
        <section class="banner">
            <img src="../assets/login.png" alt="Banner">
        </section>

        <!-- Formulario de inicio de sesión -->
        <section class="login-section">
            <form id="login-form" class="login-form" method="POST" action="">
                <h2>Iniciar sesión</h2>
                <input id="usuario" name="usuario" type="text" placeholder="Usuario" required>
                <input id="password" name="password" type="password" placeholder="Contraseña" required>
                <button type="submit">Entrar</button>
            </form>
        </section>
    </div>
</body>
</html>
