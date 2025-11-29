<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            <h3 class="subtitle">Recuperar acceso</h3>
            <p style="text-align: center; font-size: 0.9em; margin-bottom: 15px;">
                Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            <?php if (isset($_SESSION['info'])): ?>
                <div class="alert-error" style="background: #d1e7dd; color: #0f5132; border-color: #badbcc;">
                    <?php echo $_SESSION['info']; unset($_SESSION['info']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" action="../api/enviar_reset.php" method="POST">
                <label>Correo electrónico</label>
                <input type="email" name="email" required placeholder="tu@email.com">
                <button type="submit" class="btn-login">Enviar enlace</button>
            </form>

            <p class="register-text">
                <a href="login.php">Volver al Login</a>
            </p>
        </div>
    </div>
</body>
</html>