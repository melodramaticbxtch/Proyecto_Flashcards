<?php
include "../include/conexion.php"; // conexión lista

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* --------------------------
            SANEAR DATOS
    --------------------------- */

    $usuario = trim($_POST["usuario"]);
    $email = trim($_POST['email']);
    $password = trim($_POST["password"]);

    // Sanitizar email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    /* --------------------------
        VALIDAR EMAIL CORRECTO
    --------------------------- */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresá un email válido. Ejemplo: usuario@correo.com";
    }

    /* --------------------------
        VALIDAR UNICIDAD USUARIO
    --------------------------- */
    if (!$error) {
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Ese nombre de usuario ya está en uso.";
        }
        $stmt->close();
    }

    /* --------------------------
        VALIDAR UNICIDAD EMAIL
    --------------------------- */
    if (!$error) {
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Ese correo ya está registrado.";
        }
        $stmt->close();
    }

    /* --------------------------
        SI NO HAY ERRORES → REGISTRAR
    --------------------------- */
    if (!$error) {

        // Encriptar contraseña
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar usuario
        $stmt = $conexion->prepare("INSERT INTO usuario (usuario, email, contraseña_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $email, $passwordHash);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Error al registrar: " . $stmt->error;
        }

        $stmt->close();
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

        <!-- SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <?php if (!empty($error)) : ?>
        <script>
        Swal.fire({
            icon: "error",
            title: "Error en el registro",
            text: "<?php echo $error; ?>",
            confirmButtonText: "Entendido"
        });
        </script>
        <?php endif; ?>

    </body>
</html>
