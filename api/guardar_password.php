<?php
// api/guardar_password.php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // 1. VALIDACIÓN: Longitud mínima
    if (strlen($pass) < 8) {
        $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
        header("Location: ../views/restablecer.php?token=" . $token);
        exit;
    }

    // 2. VALIDACIÓN: Coincidencia
    if ($pass !== $confirm) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: ../views/restablecer.php?token=" . $token);
        exit;
    }

    // Hash de la nueva contraseña
    $new_hash = password_hash($pass, PASSWORD_DEFAULT);

    // Actualizar BD y borrar token (para que no se use 2 veces)
    $sql = "UPDATE usuarios 
            SET password = ?, reset_token = NULL, reset_token_expire = NULL 
            WHERE reset_token = ?";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$new_hash, $token])) {
        // ÉXITO: Usamos 'login_success' o 'success' estándar
        $_SESSION['success'] = "¡Contraseña actualizada correctamente! Inicia sesión.";
        header('Location: ../views/login.php');
        exit;
    } else {
        $_SESSION['error'] = "Hubo un problema al guardar. Intenta más tarde.";
        header("Location: ../views/restablecer.php?token=" . $token);
        exit;
    }
} else {
    header('Location: ../views/login.php');
    exit;
}
?>