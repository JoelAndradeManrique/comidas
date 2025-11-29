<?php
// api/login.php (Versión Segura con Hash)
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password_ingresada = $_POST['password'] ?? ''; // La que escribe el usuario (Andrade21)

    if ($email && $password_ingresada) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // AQUÍ ESTÁ EL CAMBIO CLAVE: Usamos password_verify
        // Compara la contraseña plana ('Andrade21') contra el hash en la BD
        if ($user && password_verify($password_ingresada, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];

            header('Location: ../views/sugerencias.php');
            exit;
        } else {
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            header('Location: ../views/login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Por favor completa todos los campos.";
        header('Location: ../views/login.php');
        exit;
    }
} else {
    header('Location: ../views/login.php');
    exit;
}