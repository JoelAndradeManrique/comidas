<?php
// 1. SEGURIDAD Y CONEXIÓN
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. OBTENER EL RANGO DE LA SEMANA ACTUAL
$lunes = date('Y-m-d', strtotime('monday this week'));
$domingo = date('Y-m-d', strtotime('sunday this week'));

// 3. CONSULTA SQL
$sql = "SELECT p.ingredientes 
        FROM plan_semanal ps 
        JOIN platillos p ON ps.platillo_id = p.id 
        WHERE ps.usuario_id = :uid 
        AND ps.fecha BETWEEN :inicio AND :fin";

$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id, ':inicio' => $lunes, ':fin' => $domingo]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. PROCESAMIENTO
$lista_final = [];

// DICCIONARIO MAESTRO DE INGREDIENTES
// (Aquí puedes agregar todos los casos que quieras corregir)
$sinonimos = [
    'Aceite de cocina' => 'Aceite',
    'Aceite vegetal'   => 'Aceite',
    'Jitomate'         => 'Tomate',
    'Jitomates'        => 'Tomate',
    'Tomates'          => 'Tomate',
    'Huevos'           => 'Huevo',
    'Papas'            => 'Papa',
    'Zanahorias'       => 'Zanahoria',
    'Cebollas'         => 'Cebolla',
    'Salchichas'       => 'Salchicha'
];

foreach ($resultados as $fila) {
    $ingredientes_separados = explode(',', $fila['ingredientes']);
    
    foreach ($ingredientes_separados as $ingrediente) {
        // Limpieza básica
        $limpio = trim($ingrediente);
        $limpio = ucfirst(strtolower($limpio)); // "Jitomate"
        
        // 4.5 UNIFICACIÓN DE SINÓNIMOS
        // Si la palabra está en nuestro diccionario, la cambiamos por la estándar
        if (array_key_exists($limpio, $sinonimos)) {
            $limpio = $sinonimos[$limpio];
        }

        if (!empty($limpio)) {
            $lista_final[] = $limpio;
        }
    }
}

$lista_compras = array_unique($lista_final);
sort($lista_compras);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Compras</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/ingredientes.css">
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main class="main-container">

        <section class="chef-area">
   
            
            <img src="../img/chef.png" alt="Chef" class="chef-img">
        </section>

        <section class="list-area">
            
            <div class="paper-sheet">
                <div class="sheet-header">
                    <div>
                        <h2>Lista de Compras</h2>
                        <span class="date-range">Semana: <?php echo date('d/m', strtotime($lunes)); ?> - <?php echo date('d/m', strtotime($domingo)); ?></span>
                    </div>
                    <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
                </div>

                <hr class="divider">

                <?php if (count($lista_compras) > 0): ?>
                    <ul class="shopping-list">
                        <?php foreach ($lista_compras as $item): ?>
                            <li>
                                <label class="checkbox-container">
                                    <?php echo htmlspecialchars($item); ?>
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="sheet-footer">
                        <p>MyFoods Planner</p>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <p>📝 La comanda está vacía.</p>
                        <a href="plan.php">Ir a Planificar</a>
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</body>
</html>