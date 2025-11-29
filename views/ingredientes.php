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
// Queremos la lista de compras de ESTA semana
$lunes = date('Y-m-d', strtotime('monday this week'));
$domingo = date('Y-m-d', strtotime('sunday this week'));

// 3. CONSULTA SQL
// Traemos solo la columna 'ingredientes' de los platillos planificados esta semana
$sql = "SELECT p.ingredientes 
        FROM plan_semanal ps 
        JOIN platillos p ON ps.platillo_id = p.id 
        WHERE ps.usuario_id = :uid 
        AND ps.fecha BETWEEN :inicio AND :fin";

$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id, ':inicio' => $lunes, ':fin' => $domingo]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. PROCESAMIENTO DE DATOS (EL ALGORITMO DE LISTA DE COMPRAS)
$lista_final = [];

foreach ($resultados as $fila) {
    // $fila['ingredientes'] es algo como: "Pollo, Lechuga, Tomate"
    
    // Separamos por la coma
    $ingredientes_separados = explode(',', $fila['ingredientes']);
    
    foreach ($ingredientes_separados as $ingrediente) {
        // Limpiamos espacios en blanco (ej. " Tomate" -> "Tomate") y lo convertimos a minúsculas para comparar mejor
        $limpio = trim($ingrediente);
        $limpio = ucfirst(strtolower($limpio)); // "tomate" -> "Tomate"
        
        if (!empty($limpio)) {
            $lista_final[] = $limpio;
        }
    }
}

// Eliminamos duplicados y ordenamos alfabéticamente
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
    
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; }
        .container { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-list { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;}
        .checklist { list-style: none; padding: 0; }
        .checklist li { padding: 12px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; }
        .checklist li:last-child { border-bottom: none; }
        .checklist input[type="checkbox"] { margin-right: 15px; transform: scale(1.3); cursor: pointer; }
        .checklist label { cursor: pointer; font-size: 1.1rem; color: #333; }
        
        /* Efecto tachado al marcar */
        .checklist input:checked + label { text-decoration: line-through; color: #999; }
        
        .empty-state { text-align: center; color: #777; padding: 40px; }
        .btn-print { background: #333; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .btn-print:hover { background: #555; }
    </style>
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <div class="container">
            
            <div class="header-list">
                <div>
                    <h2 style="margin:0;">Lista de Compras</h2>
                    <small style="color:#666;">Semana del <?php echo date('d/m', strtotime($lunes)); ?> al <?php echo date('d/m', strtotime($domingo)); ?></small>
                </div>
                <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
            </div>

            <?php if (count($lista_compras) > 0): ?>
                
                <p style="margin-bottom: 20px;">Basado en tu plan semanal, necesitas comprar:</p>
                
                <ul class="checklist">
                    <?php foreach ($lista_compras as $item): ?>
                        <li>
                            <input type="checkbox" id="item_<?php echo $item; ?>">
                            <label for="item_<?php echo $item; ?>"><?php echo htmlspecialchars($item); ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>

            <?php else: ?>
                
                <div class="empty-state">
                    <h3>🛒 Tu lista está vacía</h3>
                    <p>Aún no has planificado comidas para esta semana.</p>
                    <a href="sugerencias.php" style="color: #007bff; text-decoration: none;">Ir a planificar</a>
                </div>

            <?php endif; ?>

        </div>
    </main>

</body>
</html>