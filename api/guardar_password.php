<?php
require '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($pass !== $confirm) {
        die("Las contraseñas no coinciden.");
    }

    // Hash de la nueva contraseña
    $new_hash = password_hash($pass, PASSWORD_DEFAULT);

    // Actualizar BD y borrar token
    $sql = "UPDATE usuarios 
            SET password = ?, reset_token = NULL, reset_token_expire = NULL 
            WHERE reset_token = ?";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$new_hash, $token])) {
        $_SESSION['error'] = "¡Contraseña actualizada! Inicia sesión."; // Reusamos la clase de alerta del login
        header('Location: ../views/login.php');
    } else {
        echo "Error al actualizar.";
    }
}
?>