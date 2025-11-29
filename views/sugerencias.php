<?php
// 1. INICIO DE SESIÓN Y CONEXIÓN
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. LÓGICA DE FILTRADO
$categoria_actual = $_GET['categoria'] ?? 'Rápido';

// Trae 3 platillos al azar
$sql = "SELECT * FROM platillos WHERE categoria = :cat ORDER BY RAND() LIMIT 3";
$stmt = $pdo->prepare($sql);
$stmt->execute([':cat' => $categoria_actual]);
$sugerencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sugerencias Automáticas</title>
    
    <link rel="stylesheet" href="../css/header.css" />
    <link rel="stylesheet" href="../css/sugerencias.css" />
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <div class="header-content">
            <h1 class="title">Sugerencias del Chef 👨‍🍳</h1>
            <p class="subtitle">Hola, <strong><?php echo $_SESSION['user_name']; ?></strong>. Preparamos esto para ti:</p>
        </div>

        <section class="filtros">
            <a href="?categoria=Rápido" class="filtro <?php echo ($categoria_actual == 'Rápido') ? 'active' : ''; ?>">
                ⚡ Rápido
            </a>
            <a href="?categoria=Saludable" class="filtro <?php echo ($categoria_actual == 'Saludable') ? 'active' : ''; ?>">
                🥗 Saludable
            </a>
            <a href="?categoria=Económico" class="filtro <?php echo ($categoria_actual == 'Económico') ? 'active' : ''; ?>">
                💰 Económico
            </a>
        </section>

        <div class="regen-box">
            <button onclick="location.reload();" class="regen-btn">🔄 Ver otras opciones</button>
        </div>

        <section class="sugerencias-container">

            <?php if (count($sugerencias) > 0): ?>
                
                <?php foreach ($sugerencias as $platillo): ?>
                    <div class="card">
                        
                        <?php 
                            $imagen = !empty($platillo['imagen_url']) ? $platillo['imagen_url'] : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=60'; 
                        ?>
                        
                        <div class="card-image-container">
                            <img src="<?php echo $imagen; ?>" alt="Platillo">
                            <span class="category-badge"><?php echo htmlspecialchars($platillo['categoria']); ?></span>
                        </div>
                        
                        <div class="info">
                            <h3><?php echo htmlspecialchars($platillo['nombre']); ?></h3>
                            
                            <div class="meta-info">
                                <span>⏱ <?php echo $platillo['tiempo_prep'] ?? 15; ?> min</span>
                                <span>🔥 <?php echo $platillo['calorias'] ?? 300; ?> kcal</span>
                            </div>
                            
                            <p class="ingredients">
                                <?php echo htmlspecialchars($platillo['ingredientes']); ?>
                            </p>

                            <form action="../api/agregar_al_plan.php" method="POST" class="card-actions">
                                <input type="hidden" name="platillo_id" value="<?php echo $platillo['id']; ?>">
                                
                                <div class="input-group">
                                    <input type="date" 
                                        name="fecha" 
                                        required 
                                        value="<?php echo date('Y-m-d'); ?>" 
                                        min="<?php echo date('Y-m-d'); ?>"
                                        class="form-control date-input">

                                    <select name="tiempo" required class="form-control select-input">
                                        <option value="Desayuno">🍳 Desayuno</option>
                                        <option value="Almuerzo">🍲 Almuerzo</option>
                                        <option value="Cena">🌙 Cena</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn-reservar">Agregar al Plan</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="empty-message">
                    <p>No encontramos platillos en esta categoría hoy :(</p>
                </div>
            <?php endif; ?>

        </section>

    </main>

</body>
</html>