<?php
// api/login.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password_ingresada = $_POST['password'] ?? '';

    // Guardamos el email en sesión por si falla, para no obligarlo a reescribir
    $_SESSION['old_email'] = $email;

    if ($email && $password_ingresada) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password_ingresada, $user['password'])) {
            
            // Login Exitoso
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];
            
            // IMPORTANTE: Creamos una bandera para avisar al front que el login fue exitoso
            $_SESSION['login_success'] = true;
            
            // Borramos el email temporal porque ya entró
            unset($_SESSION['old_email']);

            // OJO: Lo regresamos al LOGIN para mostrar la alerta, JS se encargará de moverlo al Plan
            header('Location: ../views/login.php');
            exit;

        } else {
            $_SESSION['error'] = "Credenciales incorrectas.";
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