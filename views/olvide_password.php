<?php
session_start();

// Recuperar el correo persistente
$email_previo = $_SESSION['email_reset_persist'] ?? '';
unset($_SESSION['email_reset_persist']); // Limpiamos para que no se quede para siempre

// Verificar si debemos activar el cooldown (bloqueo temporal)
$activar_cooldown = isset($_SESSION['cooldown_activado']);
unset($_SESSION['cooldown_activado']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="logo">MyFoods</h1>
            <h3 class="subtitle">Recuperar acceso</h3>
            <p style="text-align: center; font-size: 0.9em; margin-bottom: 15px;">
                Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            <form class="login-form" action="../api/enviar_reset.php" method="POST" id="resetForm">
                <label>Correo electrónico</label>
                
                <input type="email" name="email" required 
                       placeholder="tu@email.com" 
                       value="<?php echo htmlspecialchars($email_previo); ?>">
                
                <button type="submit" class="btn-login" id="btnEnviar">Enviar enlace</button>
            </form>

            <p class="register-text">
                <a href="login.php">Volver al Login</a>
            </p>
        </div>
    </div>

    <script>
        // ALERTAS
        <?php if (isset($_SESSION['info'])): ?>
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: '<?php echo $_SESSION['info']; ?>',
                confirmButtonColor: '#3498db'
            });
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#d33'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        // --- LÓGICA DE COOLDOWN DEL BOTÓN ---
        const btn = document.getElementById('btnEnviar');
        const form = document.getElementById('resetForm');

        // 1. Si PHP dice que activemos el cooldown (porque acabamos de enviar)
        <?php if ($activar_cooldown): ?>
            iniciarCooldown(5); // 5 segundos
        <?php endif; ?>

        // 2. Prevenir doble clic al enviar (UX Básico)
        form.addEventListener('submit', function() {
            // Deshabilitamos inmediatamente para que no le den 10 veces
            btn.disabled = true;
            btn.innerHTML = 'Enviando...';
            btn.style.backgroundColor = '#95a5a6'; // Gris
        });

        // Función de cuenta regresiva
        function iniciarCooldown(segundos) {
            btn.disabled = true;
            btn.style.backgroundColor = '#95a5a6'; // Gris
            
            let contador = segundos;
            btn.innerHTML = `Reenviar en ${contador}s`;

            const intervalo = setInterval(() => {
                contador--;
                if (contador > 0) {
                    btn.innerHTML = `Reenviar en ${contador}s`;
                } else {
                    clearInterval(intervalo);
                    btn.disabled = false;
                    btn.innerHTML = 'Enviar enlace';
                    btn.style.backgroundColor = '#27ae60'; // Vuelve a verde
                }
            }, 1000);
        }
    </script>
</body>
</html>