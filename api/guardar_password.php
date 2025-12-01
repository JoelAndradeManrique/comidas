<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // VALIDACIÓN: Si no coinciden
    if ($pass !== $confirm) {
        $_SESSION['error'] = "Las contraseñas no coinciden. Inténtalo de nuevo.";
        // ¡IMPORTANTE! Regresamos con el token para que no pierda la sesión de recuperación
        header("Location: ../views/restablecer.php?token=" . $token);
        exit;
    }

    // Hash de la nueva contraseña
    $new_hash = password_hash($pass, PASSWORD_DEFAULT);

    // Actualizar BD y borrar token
    $sql = "UPDATE usuarios 
            SET password = ?, reset_token = NULL, reset_token_expire = NULL 
            WHERE reset_token = ?";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$new_hash, $token])) {
        // ÉXITO: Mandamos mensaje y redirigimos al LOGIN
        $_SESSION['success'] = "¡Contraseña actualizada! Inicia sesión.";
        header('Location: ../views/login.php');
        exit;
    } else {
        // ERROR DE BD
        $_SESSION['error'] = "Hubo un problema al guardar. Intenta más tarde.";
        header("Location: ../views/restablecer.php?token=" . $token);
        exit;
    }
} else {
    header('Location: ../views/login.php');
    exit;
}
?>