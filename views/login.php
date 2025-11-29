<?php
session_start();
// Si ya hay sesión, lo mandamos directo al plan (UX: no hacerle loguearse dos veces)
if (isset($_SESSION['user_id'])) {
    header('Location: plan.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyFoods</title>
    <link rel="stylesheet" href="../css/login.css"> 
</head>
<body>

    <div class="login-container">

        <div class="login-box">

            <h1 class="logo">MyFoods</h1>
            <h3 class="subtitle">Bienvenido de nuevo</h3>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']); // Limpiamos el error después de mostrarlo
                    ?>
                </div>
            <?php endif; ?>

            <form class="login-form" action="../api/login.php" method="POST">
                
                <label>Correo electrónico</label>
                <input type="email" name="email" placeholder="usuario@correo.com" required>

                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>

                <button type="submit" class="btn-login">Iniciar sesión</button>
            </form>

            <p class="register-text">
                ¿No tienes cuenta? <a href="#">Crear cuenta</a>
            </p>

        </div>

    </div>

</body>
</html>