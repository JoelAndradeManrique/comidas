<?php
session_start();

// Si ya hay sesión, lo mandamos directo al plan
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

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success" style="background-color: #d1e7dd; color: #0f5132; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #badbcc; text-align: center;">
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']); 
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error" style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f87171; text-align: center;">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']); 
                    ?>
                </div>
            <?php endif; ?>

            <form class="login-form" action="../api/login.php" method="POST">
                
                <label>Correo electrónico</label>
                <input type="email" name="email" placeholder="usuario@correo.com" required>

                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>

                <div style="text-align: right; margin-bottom: 15px; margin-top: 5px;">
                    <a href="olvide_password.php" style="color: #666; font-size: 0.85rem; text-decoration: none;">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit" class="btn-login">Iniciar sesión</button>
            </form>

            <p class="register-text">
                ¿No tienes cuenta? <a href="../views/registro.php">Crear cuenta</a>
            </p>

        </div>

    </div>

</body>
</html>