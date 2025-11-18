<?php
include "../include/conexion.php";
session_start();

$error = ""; // Guardará mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    // Buscar usuario
    $sql = "SELECT * FROM usuario WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        // Usuario encontrado
        $fila = $resultado->fetch_assoc();

        if (password_verify($password, $fila['contraseña_hash'])) {

            // Guardar sesión
            $_SESSION['usuario']   = $fila['usuario'];
            $_SESSION['id_usuario'] = $fila['id_usuario'];

            // Redirigir al home
            header("Location: home.php");
            exit();

        } else {
            $error = "Contraseña incorrecta";
        }

    } else {
        $error = "Usuario no encontrado";
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

        <!-- SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (!empty($error)) : ?>
        <script>
        Swal.fire({
            icon: "error",
            title: "Error al iniciar sesión",
            text: "<?php echo $error; ?>",
            confirmButtonText: "Reintentar"
        });
        </script>
        <?php endif; ?>

    </body>
</html>
