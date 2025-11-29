<?php
// api/agregar_al_plan.php
session_start();
require_once '../config/db.php';

// Validar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['user_id'];
    $platillo_id = $_POST['platillo_id'];
    $fecha = $_POST['fecha'];
    $tiempo = $_POST['tiempo'];

    // Validaciones básicas
    if (empty($platillo_id) || empty($fecha) || empty($tiempo)) {
        echo "<script>alert('Faltan datos'); window.history.back();</script>";
        exit;
    }

    try {
        // 1. Verificar si ya existe un plan para esa fecha/hora/usuario
        // (Para evitar tener 2 cenas el mismo día)
        $check = $pdo->prepare("SELECT id FROM plan_semanal 
                                WHERE usuario_id = :uid AND fecha = :fecha AND tiempo_comida = :tiempo");
        $check->execute([
            ':uid' => $usuario_id,
            ':fecha' => $fecha,
            ':tiempo' => $tiempo
        ]);

        if ($check->rowCount() > 0) {
            // OPCIÓN A: Si ya existe, avisamos y no hacemos nada
            echo "<script>
                    alert('¡Ojo! Ya tienes una comida asignada para el $tiempo del $fecha. Bórrala primero si quieres cambiarla.'); 
                    window.location.href = '../views/sugerencias.php';
                  </script>";
        } else {
            // OPCIÓN B: Insertar nuevo plan
            $sql = "INSERT INTO plan_semanal (usuario_id, platillo_id, fecha, tiempo_comida) 
                    VALUES (:uid, :pid, :fecha, :tiempo)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':uid' => $usuario_id,
                ':pid' => $platillo_id,
                ':fecha' => $fecha,
                ':tiempo' => $tiempo
            ]);

            // Redirigir al Plan Semanal para que vea su comida agendada
            echo "<script>
                    alert('¡Agregado al plan correctamente!'); 
                    window.location.href = '../views/plan.php';
                  </script>";
        }

    } catch (PDOException $e) {
        die("Error de base de datos: " . $e->getMessage());
    }

} else {
    // Si intentan entrar directo por URL
    header('Location: ../views/sugerencias.php');
}
?>