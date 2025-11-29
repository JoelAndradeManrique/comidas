<?php
// 1. CONFIGURACIÓN Y SESIÓN
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$fecha_actual = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$ayer = date('Y-m-d', strtotime($fecha_actual . ' -1 day'));
$manana = date('Y-m-d', strtotime($fecha_actual . ' +1 day'));

// --- LOGICA DIA ---
$sql_dia = "SELECT ps.*, p.nombre, p.ingredientes 
            FROM plan_semanal ps 
            JOIN platillos p ON ps.platillo_id = p.id 
            WHERE ps.usuario_id = :uid AND ps.fecha = :fecha";
$stmt = $pdo->prepare($sql_dia);
$stmt->execute([':uid' => $user_id, ':fecha' => $fecha_actual]);
$resultados_dia = $stmt->fetchAll(PDO::FETCH_ASSOC);

$plan_dia = ['Desayuno' => null, 'Almuerzo' => null, 'Cena' => null];
foreach ($resultados_dia as $fila) {
    $plan_dia[$fila['tiempo_comida']] = $fila;
}

// --- LOGICA SEMANA ---
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
    <title>Mi Agenda de Comidas</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/plan.css">
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <h1 class="title">Mi Agenda</h1>

        <section class="tabs">
            <button id="btnDia" class="tab active">Visión Diaria</button>
            <button id="btnSemana" class="tab">Visión Semanal</button>
        </section>

        <section id="planDia" class="plan-dia">

            <div class="dia-selector">
                <a href="?fecha=<?php echo $ayer; ?>" class="nav-day">‹</a>
                <h2>
                    <?php echo ($fecha_actual == date('Y-m-d')) ? "Hoy" : date('d/m/Y', strtotime($fecha_actual)); ?>
                </h2>
                <a href="?fecha=<?php echo $manana; ?>" class="nav-day">›</a>
            </div>

            <?php 
            // Iconos y etiquetas para cada tiempo
            $config_tiempos = [
                'Desayuno' => ['icono' => '🌅', 'label' => 'Mañana'],
                'Almuerzo' => ['icono' => '☀️', 'label' => 'Mediodía'],
                'Cena'     => ['icono' => '🌙', 'label' => 'Noche']
            ];

            foreach ($config_tiempos as $tiempo => $info): 
                $comida = $plan_dia[$tiempo]; 
            ?>
                
                <?php if ($comida): ?>
                    <div class="comida-card border-<?php echo $tiempo; ?>">
                        <h3><?php echo $info['icono'] . ' ' . $tiempo; ?></h3>
                        
                        <p class="meal-name"><?php echo htmlspecialchars($comida['nombre']); ?></p>
                        <p class="meal-ingredients"><?php echo htmlspecialchars($comida['ingredientes']); ?></p>
                        
                        <div class="action-buttons">
                            <a href="seleccion_comidas.php?fecha=<?php echo $fecha_actual; ?>&tiempo=<?php echo $tiempo; ?>" 
                               class="btn-action btn-edit">✏️ Cambiar</a>
                            
                            <a href="../api/eliminar_plan.php?id=<?php echo $comida['id']; ?>&fecha=<?php echo $fecha_actual; ?>" 
                               class="btn-action btn-delete"
                               onclick="return confirm('¿Quitar del plan?');">🗑️</a>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <h3><?php echo $info['icono'] . ' ' . $tiempo; ?></h3>
                        <p class="sin-asignar">Espacio disponible</p>
                        <a href="seleccion_comidas.php?fecha=<?php echo $fecha_actual; ?>&tiempo=<?php echo $tiempo; ?>" 
                           class="btn-action btn-add">
                           + Agregar
                        </a>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>

        </section>

        <section id="planSemana" class="plan-semana hidden">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Desayuno</th>
                            <th>Almuerzo</th>
                            <th>Cena</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        for ($i = 0; $i < 7; $i++): 
                            $dia_loop = date('Y-m-d', strtotime($lunes_semana . " +$i days"));
                            $nombre_dia = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$i];
                            $es_hoy = ($dia_loop == date('Y-m-d'));
                        ?>
                            <tr class="<?php echo $es_hoy ? 'is-today' : ''; ?>">
                                <td style="font-weight: bold;">
                                    <?php echo $nombre_dia; ?>
                                    <div style="font-size:0.75rem; color:#888; font-weight:normal;">
                                        <?php echo date('d/m', strtotime($dia_loop)); ?>
                                    </div>
                                </td>
                                <td><?php echo $grid_semanal[$dia_loop]['Desayuno'] ?? '<span style="color:#ddd;">-</span>'; ?></td>
                                <td><?php echo $grid_semanal[$dia_loop]['Almuerzo'] ?? '<span style="color:#ddd;">-</span>'; ?></td>
                                <td><?php echo $grid_semanal[$dia_loop]['Cena'] ?? '<span style="color:#ddd;">-</span>'; ?></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
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