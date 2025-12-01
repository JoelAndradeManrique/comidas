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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            
            <?php if ($token_valido): ?>
                <h3 class="subtitle">Restablecer Contraseña</h3>
                
                <form class="login-form" action="../api/guardar_password.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <label>Nueva contraseña <span style="color:red">*</span></label>
                    <div class="password-container">
                        <input type="password" name="password" id="pass1" 
                               required placeholder="Mínimo 8 caracteres">
                        <span class="material-icons toggle-password" onclick="togglePassword('pass1', this)">visibility</span>
                    </div>
                    
                    <label>Confirmar contraseña <span style="color:red">*</span></label>
                    <div class="password-container">
                        <input type="password" name="confirm_password" id="pass2" 
                               required placeholder="Repite la contraseña">
                        <span class="material-icons toggle-password" onclick="togglePassword('pass2', this)">visibility</span>
                    </div>
                    
                    <button type="submit" class="btn-login">Guardar Cambios</button>
                </form>
            
            <?php else: ?>
                <div style="text-align: center; padding: 20px;">
                    <span class="material-icons" style="font-size: 3rem; color: #e74c3c;">link_off</span>
                    <p style="color: #666; margin-top: 10px;">Este enlace es inválido o ha expirado.</p>
                </div>
                <p class="register-text"><a href="olvide_password.php">Solicitar uno nuevo</a></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // 1. FUNCIÓN PARA VER CONTRASEÑA (REUTILIZABLE)
        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                iconElement.textContent = "visibility_off"; 
            } else {
                input.type = "password";
                iconElement.textContent = "visibility"; 
            }
        }

        // 2. ALERTAS
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#f39c12',
                confirmButtonText: 'Corregir'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>

</body>
</html>