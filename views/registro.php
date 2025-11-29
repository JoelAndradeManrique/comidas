<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: sugerencias.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - MyFoods</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            <h3 class="subtitle">Crear nueva cuenta</h3>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" action="../api/registro.php" method="POST">
                
                <label>Nombre completo</label>
                <input type="text" name="nombre" placeholder="Ej. Joel Andrade" required>

                <label>Correo electrónico</label>
                <input type="email" name="email" placeholder="usuario@correo.com" required>

                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">

                <label>Confirmar contraseña</label>
                <input type="password" name="confirm_password" placeholder="Repite la contraseña" required>

                <button type="submit" class="btn-login">Registrarse</button>
            </form>

            <p class="register-text">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>

</body>
</html>