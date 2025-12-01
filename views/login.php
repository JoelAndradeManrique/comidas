<?php
session_start();

// LÓGICA DE REDIRECCIÓN INTELIGENTE
// Si ya está logueado...
if (isset($_SESSION['user_id'])) {
    // ...PERO NO es porque se acaba de loguear ahorita mismo (queremos ver la alerta)
    if (!isset($_SESSION['login_success'])) {
        header('Location: sugerencias.php');
        exit;
    }
}

// Recuperar el email escrito si hubo error
$email_previo = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_email']); // Limpiar para la próxima
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyFoods</title>
    <link rel="stylesheet" href="../css/login.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            <h3 class="subtitle">Bienvenido de nuevo</h3>

            <form class="login-form" action="../api/login.php" method="POST">
                
                <label>Correo electrónico</label>
                <input type="email" name="email" 
                       placeholder="usuario@correo.com" 
                       value="<?php echo htmlspecialchars($email_previo); ?>" 
                       required>

                <label>Contraseña</label>
                
                <div class="password-container">
                    <input type="password" name="password" id="passwordInput" placeholder="••••••••" required>
                    <span class="material-icons toggle-password" onclick="togglePassword()">visibility</span>
                </div>

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

   <script>
        // 1. FUNCIÓN PARA MOSTRAR/OCULTAR CONTRASEÑA
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.querySelector('.toggle-password');
            
            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "visibility_off"; 
            } else {
                input.type = "password";
                icon.textContent = "visibility"; 
            }
        }

        // 2. ALERTAS DE SWEETALERT

        // --- CASO: ÉXITO GENERAL (Registro o Recuperación de Password) ---
        // ESTE ERA EL QUE FALTABA
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Excelente!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonColor: '#27ae60',
                confirmButtonText: 'Entendido'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        // --- CASO: LOGIN EXITOSO (Especial con redirección automática) ---
        <?php if (isset($_SESSION['login_success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Bienvenido!',
                text: 'Iniciando sesión...',
                timer: 2000, 
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'plan.php';
            });
            <?php unset($_SESSION['login_success']); ?>
        <?php endif; ?>

        // --- CASO: ERROR (Credenciales o Fallos) ---
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Ups...',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Intentar de nuevo'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>

</body>
</html>