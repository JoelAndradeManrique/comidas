<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // 1. GUARDAR CORREO PARA QUE NO SE BORRE (PERSISTENCIA)
    $_SESSION['email_reset_persist'] = $email;

    // ... (resto de validaciones) ...
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expira = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $update = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expire = ? WHERE email = ?");
        $update->execute([$token, $expira, $email]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'joelmanrique38@gmail.com'; // TU CORREO
            $mail->Password   = 'dlav fukr gfzt zvgy';         // TU CLAVE DE APP (recuerda ponerla)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('andrademanrique38@gmail.com', 'Soporte MyFoods');
            $mail->addAddress($email, $user['nombre']);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Password - MyFoods';
            
            // Ajusta localhost si es necesario
            $enlace = "http://localhost/comidas/views/restablecer.php?token=" . $token;
            
             $mail->Body    = "
                <h1>Hola, {$user['nombre']}</h1>
                <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                <p>Haz clic en el siguiente enlace para crear una nueva:</p>
                <a href='$enlace' style='background:#222; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Restablecer Contraseña</a>
                <p><small>Este enlace expira en 1 hora.</small></p>
            ";

            $mail->send();
            $_SESSION['info'] = "Correo enviado. Revisa tu bandeja.";
            // Bandera para activar el cooldown en el front
            $_SESSION['cooldown_activado'] = true; 

        } catch (Exception $e) {
            $_SESSION['error'] = "Error al enviar: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['info'] = "Si el correo existe, recibirás el enlace.";
        $_SESSION['cooldown_activado'] = true; // También activamos cooldown por seguridad
    }

    header('Location: ../views/olvide_password.php');
    exit;
}
?>