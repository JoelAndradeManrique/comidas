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
        $check = $pdo->prepare("SELECT id FROM plan_semanal 
                                WHERE usuario_id = :uid AND fecha = :fecha AND tiempo_comida = :tiempo");
        $check->execute([
            ':uid' => $usuario_id,
            ':fecha' => $fecha,
            ':tiempo' => $tiempo
        ]);
        
        $registro_existente = $check->fetch(PDO::FETCH_ASSOC);

        if ($registro_existente) {
            // --- CASO A: ACTUALIZAR (Cambiar el platillo existente) ---
            $sql = "UPDATE plan_semanal SET platillo_id = :pid 
                    WHERE id = :id_plan";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':pid' => $platillo_id,
                ':id_plan' => $registro_existente['id']
            ]);
            
            $mensaje = "¡Menú actualizado correctamente!";

        } else {
            // --- CASO B: INSERTAR (Crear nuevo registro) ---
            $sql = "INSERT INTO plan_semanal (usuario_id, platillo_id, fecha, tiempo_comida) 
                    VALUES (:uid, :pid, :fecha, :tiempo)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':uid' => $usuario_id,
                ':pid' => $platillo_id,
                ':fecha' => $fecha,
                ':tiempo' => $tiempo
            ]);
            
            $mensaje = "¡Agregado al plan correctamente!";
        }

        // Redirigir al Plan Semanal en la fecha correcta
        echo "<script>
                // alert('$mensaje'); // Opcional: Quitar si quieres que sea más fluido
                window.location.href = '../views/plan.php?fecha=$fecha';
              </script>";

    } catch (PDOException $e) {
        die("Error de base de datos: " . $e->getMessage());
    }

} else {
    header('Location: ../views/sugerencias.php');
}
?>