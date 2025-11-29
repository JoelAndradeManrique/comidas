<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar librerías de Composer (ajusta la ruta si tu carpeta vendor está en otro lado)
require '../vendor/autoload.php';
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // 1. Verificar si el email existe
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 2. Generar Token Único y Expiración (1 hora)
        $token = bin2hex(random_bytes(50));
        $expira = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 3. Guardar Token en BD
        $update = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expire = ? WHERE email = ?");
        $update->execute([$token, $expira, $email]);

        // 4. Configurar PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del Servidor SMTP de Google
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'joelmanrique38@gmail.com'; // <--- PON TU GMAIL AQUÍ
            $mail->Password   = 'dlav fukr gfzt zvgy'; // <--- PON TU CÓDIGO DE 16 LETRAS AQUÍ
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Remitente y Destinatario
            $mail->setFrom('tucorreo@gmail.com', 'Soporte MyFoods');
            $mail->addAddress($email, $user['nombre']);

            // Contenido del Correo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Password - MyFoods';
            
            // OJO: Ajusta esta URL a la ruta real de tu proyecto en localhost
            $enlace = "http://localhost/comidas/views/restablecer.php?token=" . $token;
            
            $mail->Body    = "
                <h1>Hola, {$user['nombre']}</h1>
                <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                <p>Haz clic en el siguiente enlace para crear una nueva:</p>
                <a href='$enlace' style='background:#222; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Restablecer Contraseña</a>
                <p><small>Este enlace expira en 1 hora.</small></p>
            ";

            $mail->send();
            $_SESSION['info'] = "Revisa tu correo (y spam), te hemos enviado el enlace.";

        } catch (Exception $e) {
            $_SESSION['error'] = "Error al enviar correo: {$mail->ErrorInfo}";
        }
    } else {
        // Por seguridad, no decimos si el correo existe o no, pero simulamos que se envió
        $_SESSION['info'] = "Si el correo existe, recibirás el enlace.";
    }

    header('Location: ../views/olvide_password.php');
    exit;
}
?>