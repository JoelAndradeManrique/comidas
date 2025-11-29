<?php
// api/eliminar_plan.php
session_start();
require_once '../config/db.php';

// Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit;
}

if (isset($_GET['id']) && isset($_GET['fecha'])) {
    $plan_id = $_GET['id'];
    $usuario_id = $_SESSION['user_id'];
    $fecha_retorno = $_GET['fecha']; // Para saber a qué día del calendario regresar

    // Borramos SOLO si el plan pertenece a este usuario (Seguridad básica)
    $stmt = $pdo->prepare("DELETE FROM plan_semanal WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$plan_id, $usuario_id]);
    
    // Regresamos al plan en la fecha donde estaba
    header("Location: ../views/plan.php?fecha=" . $fecha_retorno);
    exit;
} else {
    header('Location: ../views/plan.php');
}
?>