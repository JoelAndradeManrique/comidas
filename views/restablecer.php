<?php
require '../config/db.php';
session_start();

// Verificar token
$token = $_GET['token'] ?? '';
$token_valido = false;

if ($token) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_token_expire > NOW()");
    $stmt->execute([$token]);
    if ($stmt->rowCount() > 0) {
        $token_valido = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            
            <?php if ($token_valido): ?>
                <h3 class="subtitle">Crear nueva contraseña</h3>
                
                <form class="login-form" action="../api/guardar_password.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <label>Nueva contraseña</label>
                    <input type="password" name="password" required minlength="8" placeholder="Mínimo 8 caracteres">
                    
                    <label>Confirmar contraseña</label>
                    <input type="password" name="confirm_password" required placeholder="Repite la contraseña">
                    
                    <button type="submit" class="btn-login">Guardar Cambios</button>
                </form>
            
            <?php else: ?>
                <div style="text-align: center; padding: 20px;">
                    <span style="font-size: 3rem;">🚫</span>
                    <p style="color: #666; margin-top: 10px;">El enlace es inválido o ha expirado.</p>
                </div>
                <p class="register-text"><a href="olvide_password.php">Solicitar uno nuevo</a></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#d33'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>

</body>
</html>