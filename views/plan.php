<?php
// 1. CONFIGURACIÓN Y SESIÓN
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. MANEJO DE FECHAS (La magia de la navegación)
// Si viene fecha por URL (?fecha=2025-12-01) la usamos, si no, usamos HOY.
$fecha_actual = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Calculamos día anterior y siguiente para las flechas
$ayer = date('Y-m-d', strtotime($fecha_actual . ' -1 day'));
$manana = date('Y-m-d', strtotime($fecha_actual . ' +1 day'));

// --- LOGICA 1: CARGAR PLAN DEL DÍA SELECCIONADO ---
$sql_dia = "SELECT ps.*, p.nombre, p.ingredientes 
            FROM plan_semanal ps 
            JOIN platillos p ON ps.platillo_id = p.id 
            WHERE ps.usuario_id = :uid AND ps.fecha = :fecha";
$stmt = $pdo->prepare($sql_dia);
$stmt->execute([':uid' => $user_id, ':fecha' => $fecha_actual]);
$resultados_dia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar en un array fácil de usar: $plan_dia['Desayuno'] = {datos...}
$plan_dia = ['Desayuno' => null, 'Almuerzo' => null, 'Cena' => null];
foreach ($resultados_dia as $fila) {
    $plan_dia[$fila['tiempo_comida']] = $fila;
}

// --- LOGICA 2: CARGAR PLAN DE LA SEMANA (Lunes a Domingo) ---
// Encontrar el lunes de la semana de la fecha actual
$lunes_semana = date('Y-m-d', strtotime('monday this week', strtotime($fecha_actual)));
$domingo_semana = date('Y-m-d', strtotime('sunday this week', strtotime($fecha_actual)));

$sql_semana = "SELECT ps.*, p.nombre 
               FROM plan_semanal ps 
               JOIN platillos p ON ps.platillo_id = p.id 
               WHERE ps.usuario_id = :uid 
               AND ps.fecha BETWEEN :inicio AND :fin";
$stmt = $pdo->prepare($sql_semana);
$stmt->execute([':uid' => $user_id, ':inicio' => $lunes_semana, ':fin' => $domingo_semana]);
$resultados_semana = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar matriz: $grid[fecha][tiempo] = nombre_platillo
$grid_semanal = [];
foreach ($resultados_semana as $fila) {
    $grid_semanal[$fila['fecha']][$fila['tiempo_comida']] = $fila['nombre'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Plan Diario / Semanal</title>
    
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/plan.css">
    
    <style>
        /* Ajuste visual para cuando no hay comida asignada */
        .sin-asignar { color: #999; font-style: italic; }
        /* Estilo simple para las flechas como enlaces */
        .nav-day { text-decoration: none; font-size: 1.5rem; color: #333; padding: 0 10px; }
        .nav-day:hover { color: #007bff; }
    </style>
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <h1 class="title">Planificación de Comidas</h1>

        <section class="tabs">
            <button id="btnDia" class="tab active">Día</button>
            <button id="btnSemana" class="tab">Semana</button>
        </section>

        <section id="planDia" class="plan-dia">

            <div class="dia-selector">
                <a href="?fecha=<?php echo $ayer; ?>" class="nav-day">←</a>
                
                <h2>
                    <?php 
                        // Mostrar "Hoy" si coincide, si no, la fecha
                        echo ($fecha_actual == date('Y-m-d')) ? "Hoy" : date('d/m/Y', strtotime($fecha_actual)); 
                    ?>
                </h2>
                
                <a href="?fecha=<?php echo $manana; ?>" class="nav-day">→</a>
            </div>

            <?php 
            $tiempos = ['Desayuno', 'Almuerzo', 'Cena'];
            foreach ($tiempos as $tiempo): 
                $comida = $plan_dia[$tiempo]; // Sacamos los datos si existen
            ?>
                <div class="comida-card">
                    <h3><?php echo $tiempo; ?></h3>
                    
                    <?php if ($comida): ?>
                            <p style="font-weight: bold; font-size: 1.1em;"><?php echo htmlspecialchars($comida['nombre']); ?></p>
                                <p style="font-size: 0.9em; color: #666;"><?php echo htmlspecialchars($comida['ingredientes']); ?></p>
                                
                                <div style="margin-top: 8px;">
                                    <a href="seleccion_comidas.php?fecha=<?php echo $fecha_actual; ?>&tiempo=<?php echo $tiempo; ?>" 
                                    class="editar-btn" style="text-decoration: none;">
                                    Cambiar
                                    </a>

                                    <a href="../api/eliminar_plan.php?id=<?php echo $comida['id']; ?>&fecha=<?php echo $fecha_actual; ?>" 
                                    class="editar-btn" 
                                    style="background-color: #dc3545; margin-left: 5px; text-decoration: none;"
                                    onclick="return confirm('¿Quitar esta comida del plan?');">
                                    🗑️
                                    </a>
                                </div>
                        
                        <?php else: ?>
                            <p class="sin-asignar">No has planeado nada aún.</p>
                            
                            <a href="seleccion_comidas.php?fecha=<?php echo $fecha_actual; ?>&tiempo=<?php echo $tiempo; ?>" 
                            class="editar-btn" style="background-color: #28a745; text-decoration: none; display:inline-block;">
                            + Agregar
                            </a>
                        <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </section>

        <section id="planSemana" class="plan-semana hidden">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Desayuno</th>
                        <th>Almuerzo</th> <th>Cena</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Generamos los 7 días a partir del lunes
                    for ($i = 0; $i < 7; $i++): 
                        $dia_loop = date('Y-m-d', strtotime($lunes_semana . " +$i days"));
                        
                        // Nombres de días en español (truco rápido)
                        $nombres_dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                        $nombre_dia = $nombres_dias[$i];
                    ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px; font-weight: bold;">
                                <?php echo $nombre_dia; ?> <br>
                                <span style="font-size:0.8em; color:#888;"><?php echo date('d/m', strtotime($dia_loop)); ?></span>
                            </td>
                            
                            <td><?php echo $grid_semanal[$dia_loop]['Desayuno'] ?? '-'; ?></td>
                            
                            <td><?php echo $grid_semanal[$dia_loop]['Almuerzo'] ?? '-'; ?></td>
                            
                            <td><?php echo $grid_semanal[$dia_loop]['Cena'] ?? '-'; ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </section>

    </main>

    <script>
        const btnDia = document.getElementById("btnDia");
        const btnSemana = document.getElementById("btnSemana");
        const planDia = document.getElementById("planDia");
        const planSemana = document.getElementById("planSemana");

        btnDia.addEventListener("click", () => {
            btnDia.classList.add("active");
            btnSemana.classList.remove("active");
            planDia.classList.remove("hidden");
            planSemana.classList.add("hidden");
        });

        btnSemana.addEventListener("click", () => {
            btnSemana.classList.add("active");
            btnDia.classList.remove("active");
            planSemana.classList.remove("hidden");
            planDia.classList.add("hidden");
        });
    </script>

</body>
</html>