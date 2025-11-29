<?php
// 1. CONFIGURACIÓN
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. CAPTURAR VARIABLES DE CONTEXTO
$fecha_seleccionada = $_GET['fecha'] ?? date('Y-m-d');
$manana = date('Y-m-d', strtotime('+1 day')); // Variable para el botón "Mañana"

// Tiempo: Mañana -> Desayuno, Tarde -> Almuerzo, Noche -> Cena
$tiempo_actual = $_GET['tiempo'] ?? 'Desayuno'; 

// Busqueda
$busqueda = $_GET['q'] ?? '';

// Verificar qué tengo asignado ya
$sql_check = "SELECT platillo_id FROM plan_semanal 
              WHERE usuario_id = :uid AND fecha = :fecha AND tiempo_comida = :tiempo";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([
    ':uid' => $_SESSION['user_id'],
    ':fecha' => $fecha_seleccionada,
    ':tiempo' => $tiempo_actual
]);
$fila_actual = $stmt_check->fetch(PDO::FETCH_ASSOC);
$id_platillo_actual = $fila_actual ? $fila_actual['platillo_id'] : null;

// 3. CONSULTA A LA BD (CATÁLOGO)
$sql = "SELECT * FROM platillos";
$params = [];

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
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <h1 class="title">Seleccionar Comida</h1>
        
        <div class="date-tabs-container">
            <span style="color: #666; font-size: 0.9rem;">Planificando para:</span>
            
            <div class="date-tabs">
                <a href="?fecha=<?php echo date('Y-m-d'); ?>&tiempo=<?php echo $tiempo_actual; ?>" 
                   class="date-tab <?php echo ($fecha_seleccionada == date('Y-m-d')) ? 'active' : ''; ?>">
                   Hoy
                </a>

                <a href="?fecha=<?php echo $manana; ?>&tiempo=<?php echo $tiempo_actual; ?>" 
                   class="date-tab <?php echo ($fecha_seleccionada == $manana) ? 'active' : ''; ?>">
                   Mañana
                </a>
                
                <input type="date" 
                    value="<?php echo $fecha_seleccionada; ?>" 
                    min="<?php echo date('Y-m-d'); ?>" 
                    onchange="window.location.href='?tiempo=<?php echo $tiempo_actual; ?>&fecha='+this.value"
                    class="date-picker-input">
            </div>
        </div>

        <section class="tabs">
            <a href="?tiempo=Desayuno&fecha=<?php echo $fecha_seleccionada; ?>" 
               class="tab <?php echo ($tiempo_actual == 'Desayuno') ? 'active' : ''; ?>">
               🌅 Mañana
            </a>
            <a href="?tiempo=Almuerzo&fecha=<?php echo $fecha_seleccionada; ?>" 
               class="tab <?php echo ($tiempo_actual == 'Almuerzo') ? 'active' : ''; ?>">
               ☀️ Tarde
            </a>
            <a href="?tiempo=Cena&fecha=<?php echo $fecha_seleccionada; ?>" 
               class="tab <?php echo ($tiempo_actual == 'Cena') ? 'active' : ''; ?>">
               🌙 Noche
            </a>
        </section>

        <section class="search-box">
            <form action="" method="GET" style="width: 100%; display: flex;">
                <input type="hidden" name="tiempo" value="<?php echo $tiempo_actual; ?>">
                <input type="hidden" name="fecha" value="<?php echo $fecha_seleccionada; ?>">
                
                    <input type="text" 
                        id="searchInput"  name="q" 
                        placeholder="Buscar por nombre o ingrediente..." 
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                        style="flex-grow: 1;">
                
                <button type="submit" class="btn-search">🔍</button>
            </form>
        </section>

        <section class="comidas-container">

            <?php if (count($platillos) > 0): ?>
                <?php foreach ($platillos as $p): ?>
                    <div class="card">
                        <?php 
                            $img = !empty($p['imagen_url']) ? $p['imagen_url'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=60'; 
                        ?>
                        <img src="<?php echo $img; ?>" alt="Comida">
                        
                        <div class="info">
                            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($p['categoria']); ?></p>
                            
                            <span class="ingredientes-text">
                                <?php echo substr(htmlspecialchars($p['ingredientes']), 0, 50) . '...'; ?>
                            </span>

                            <?php if ($p['id'] == $id_platillo_actual): ?>
                                <button type="button" class="btn" style="background-color: #95a5a6; cursor: default; opacity: 0.8;">
                                    ✅ Seleccionado
                                </button>
                            <?php else: ?>
                                <form action="../api/agregar_al_plan.php" method="POST">
                                    <input type="hidden" name="platillo_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="fecha" value="<?php echo $fecha_seleccionada; ?>">
                                    <input type="hidden" name="tiempo" value="<?php echo $tiempo_actual; ?>">
                                    <button type="submit" class="btn">Seleccionar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; width: 100%; grid-column: 1 / -1;">No encontramos platillos con ese nombre :(</p>
            <?php endif; ?>

        </section>

    </main>
    <script>
    // BÚSQUEDA EN TIEMPO REAL
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.card');

    searchInput.addEventListener('keyup', function(e) {
        const termino = e.target.value.toLowerCase();

        cards.forEach(card => {
            // Obtenemos todo el texto dentro de la tarjeta (título, categoría, ingredientes)
            const contenido = card.innerText.toLowerCase();

            // Si el término está incluido, mostramos; si no, ocultamos
            if (contenido.includes(termino)) {
                card.style.display = 'flex'; // Usamos flex porque así está en tu CSS original
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Opcional: Evitar que al dar ENTER se recargue la página (para que sea 100% live)
    document.querySelector('.search-box form').addEventListener('submit', function(e) {
        e.preventDefault(); 
    });
</script>

</body>
</html>