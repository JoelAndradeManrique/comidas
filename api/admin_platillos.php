<?php
// api/admin_platillos.php
session_start();
require_once '../config/db.php';

// SEGURIDAD: Solo el ADMIN puede entrar aquí
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    die("Acceso denegado. No eres administrador.");
}

// 1. ELIMINAR (Viene por GET)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM platillos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: ../views/editor_comidas.php');
    exit;
}

// 2. CREAR O EDITAR (Viene por POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? ''; // Si trae ID es Edición, si no, es Nuevo
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $ingredientes = $_POST['ingredientes'];
    $imagen_url = $_POST['imagen_url'];

    try {
        if ($id) {
            // --- ACTUALIZAR EXISTENTE ---
            $sql = "UPDATE platillos SET nombre=?, categoria=?, ingredientes=?, imagen_url=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $categoria, $ingredientes, $imagen_url, $id]);
        } else {
            // --- CREAR NUEVO ---
            $sql = "INSERT INTO platillos (nombre, categoria, ingredientes, imagen_url) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $categoria, $ingredientes, $imagen_url]);
        }
        
        header('Location: ../views/editor_comidas.php');

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>