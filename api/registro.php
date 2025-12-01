<?php
// api/registro.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Guardamos datos previos para no borrar todo si falla
    $_SESSION['old_nombre'] = $nombre;
    $_SESSION['old_email'] = $email;

    // 1. Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header('Location: ../views/registro.php');
        exit;
    }

    // VALIDACIÓN NUEVA: Mínimo 8 caracteres
    if (strlen($password) < 8) {
        $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
        header('Location: ../views/registro.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header('Location: ../views/registro.php');
        exit;
    }

    try {
        // 2. Verificar si el correo ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Ese correo ya está registrado.";
            header('Location: ../views/registro.php');
            exit;
        }

        // 3. Crear el usuario
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $rol = 'CLIENTE';

        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :pass, :rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':pass' => $password_hash,
            ':rol' => $rol
        ]);

        // 4. Éxito
        unset($_SESSION['old_nombre']);
        unset($_SESSION['old_email']);
        
        $_SESSION['success'] = "¡Cuenta creada con éxito! Ahora inicia sesión.";
        header('Location: ../views/login.php');

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en base de datos: " . $e->getMessage();
        header('Location: ../views/registro.php');
    }

} else {
    header('Location: ../views/registro.php');
}
?>