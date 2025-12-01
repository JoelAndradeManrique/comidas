<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: plan.php');
    exit;
}

// Recuperar datos si hubo error
$nombre_previo = $_SESSION['old_nombre'] ?? '';
$email_previo = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_nombre'], $_SESSION['old_email']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - MyFoods</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            <h3 class="subtitle">Crear nueva cuenta</h3>

            <form class="login-form" action="../api/registro.php" method="POST">
                
                <label>Nombre completo <span style="color:red">*</span></label>
                <input type="text" name="nombre" placeholder="Ej. Joel Andrade" 
                       value="<?php echo htmlspecialchars($nombre_previo); ?>" required>

                <label>Correo electrónico <span style="color:red">*</span></label>
                <input type="email" name="email" placeholder="usuario@correo.com" 
                       value="<?php echo htmlspecialchars($email_previo); ?>" required>

                <label>Contraseña <span style="color:red">*</span></label>
                <div class="password-container">
                    <input type="password" name="password" id="pass1" 
                           placeholder="Mínimo 8 caracteres" required>
                    <span class="material-icons toggle-password" onclick="togglePassword('pass1', this)">visibility</span>
                </div>

                <label>Confirmar contraseña <span style="color:red">*</span></label>
                <div class="password-container">
                    <input type="password" name="confirm_password" id="pass2" 
                           placeholder="Repite la contraseña" required>
                    <span class="material-icons toggle-password" onclick="togglePassword('pass2', this)">visibility</span>
                </div>

                <button type="submit" class="btn-login">Registrarse</button>
            </form>

            <p class="register-text">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </p>
        </div>
    </div>

    <script>
        // 1. FUNCIÓN DINÁMICA PARA EL OJITO
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

        // 2. MANEJO DE ALERTAS (SWEETALERT)
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