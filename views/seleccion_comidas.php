<?php
// 1. CONFIGURACIÓN
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. CAPTURAR VARIABLES DE CONTEXTO
// Fecha: Si no viene en la URL, asumimos HOY.
$fecha_seleccionada = $_GET['fecha'] ?? date('Y-m-d');

// Tiempo: Mapeamos los tabs visuales a los valores de la BD
// Mañana -> Desayuno, Tarde -> Almuerzo, Noche -> Cena
$tiempo_actual = $_GET['tiempo'] ?? 'Desayuno'; 

// Busqueda
$busqueda = $_GET['q'] ?? '';

// 3. CONSULTA A LA BD (CATÁLOGO)
$sql = "SELECT * FROM platillos";
$params = [];

// Si hay búsqueda, filtramos
if ($busqueda) {
    $sql .= " WHERE nombre LIKE :busqueda OR ingredientes LIKE :busqueda";
    $params[':busqueda'] = "%$busqueda%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$platillos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seleccionar Comidas</title>
    
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/seleccion_comidas.css" />
    
    <style>
        /* Pequeño ajuste para que los tabs (enlaces) se vean bien */
        .tab { text-decoration: none; cursor: pointer; display: inline-block; }
        /* Para destacar ingredientes */
        .ingredientes-text { font-size: 0.8rem; color: #666; margin-bottom: 10px; display: block;}
    </style>
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <h1 class="title">Seleccionar Comida</h1>
        <p style="text-align:center; color:#555; margin-bottom: 10px;">
            Para el día: <strong><?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?></strong>
        </p>

        <section class="tabs">
            <a href="?tiempo=Desayuno&fecha=<?php echo $fecha_seleccionada; ?>" 
               class="tab <?php echo ($tiempo_actual == 'Desayuno') ? 'active' : ''; ?>">
               Mañana
            </a>
            <a href="?tiempo=Almuerzo&fecha=<?php echo $fecha_seleccionada; ?>" 
               class="tab <?php echo ($tiempo_actual == 'Almuerzo') ? 'active' : ''; ?>">
               Tarde
            </a>
            <a href="?tiempo=Cena&fecha=<?php echo $fecha_seleccionada; ?>" 
               class="tab <?php echo ($tiempo_actual == 'Cena') ? 'active' : ''; ?>">
               Noche
            </a>
        </section>

        <section class="search-box">
            <form action="" method="GET" style="width: 100%; display: flex;">
                <input type="hidden" name="tiempo" value="<?php echo $tiempo_actual; ?>">
                <input type="hidden" name="fecha" value="<?php echo $fecha_seleccionada; ?>">
                
                <input type="text" name="q" 
                       placeholder="Buscar por nombre o ingrediente..." 
                       value="<?php echo htmlspecialchars($busqueda); ?>"
                       style="flex-grow: 1;">
                
                <button type="submit" class="btn" style="width: auto; margin-left: 10px;">🔍</button>
            </form>
        </section>

        <section class="comidas-container">

            <?php if (count($platillos) > 0): ?>
                <?php foreach ($platillos as $p): ?>
                    <div class="card">
                        <?php $img = $p['imagen_url'] ? $p['imagen_url'] : 'https://via.placeholder.com/120?text=' . substr($p['nombre'], 0, 1); ?>
                        <img src="<?php echo $img; ?>" alt="Comida">
                        
                        <div class="info">
                            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            
                            <p>Categoría: <?php echo htmlspecialchars($p['categoria']); ?></p>
                            
                            <span class="ingredientes-text">
                                <?php echo substr(htmlspecialchars($p['ingredientes']), 0, 50) . '...'; ?>
                            </span>

                            <form action="../api/agregar_al_plan.php" method="POST">
                                <input type="hidden" name="platillo_id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="fecha" value="<?php echo $fecha_seleccionada; ?>">
                                <input type="hidden" name="tiempo" value="<?php echo $tiempo_actual; ?>">
                                
                                <button type="submit" class="btn">Seleccionar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; width: 100%;">No encontramos platillos con ese nombre :(</p>
            <?php endif; ?>

        </section>

    </main>

</body>
</html>