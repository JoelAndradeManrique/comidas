<?php
// 1. INICIO DE SESIÓN Y CONEXIÓN
session_start();
require_once '../config/db.php';

// Si no está logueado, fuera de aquí
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. LÓGICA DE FILTRADO
// Si no hay categoría en la URL, usamos 'Rápido' por defecto
$categoria_actual = $_GET['categoria'] ?? 'Rápido';

// Preparamos la consulta: Trae 3 platillos al azar de la categoría seleccionada
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
    
    <style>
        /* Esto hace que los links <a> se vean y comporten como tus botones anteriores */
        .filtro {
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            /* Aquí hereda tus estilos de .filtro del CSS externo */
        }
    </style>
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main>
        <h1 class="title">Sugerencias Automáticas</h1>
        <p style="text-align: center; color: #666;">Hola, <strong><?php echo $_SESSION['user_name']; ?></strong>. ¿Qué se te antoja hoy?</p>

        <section class="filtros">
            <a href="?categoria=Rápido" class="filtro <?php echo ($categoria_actual == 'Rápido') ? 'active' : ''; ?>">
                Rápido
            </a>
            <a href="?categoria=Saludable" class="filtro <?php echo ($categoria_actual == 'Saludable') ? 'active' : ''; ?>">
                Saludable
            </a>
            <a href="?categoria=Económico" class="filtro <?php echo ($categoria_actual == 'Económico') ? 'active' : ''; ?>">
                Económico
            </a>
        </section>

        <div class="regen-box">
            <button onclick="location.reload();" class="regen-btn">🔄 Generar nuevas sugerencias</button>
        </div>

        <section class="sugerencias-container">

            <?php if (count($sugerencias) > 0): ?>
                
                <?php foreach ($sugerencias as $platillo): ?>
                    <div class="card">
                        <?php $imagen = $platillo['imagen_url'] ? $platillo['imagen_url'] : 'https://via.placeholder.com/200?text=Sin+Foto'; ?>
                        
                        <img src="<?php echo $imagen; ?>" alt="Platillo">
                        
                        <div class="info">
                            <h3><?php echo htmlspecialchars($platillo['nombre']); ?></h3>
                            
                            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($platillo['categoria']); ?></p>
                            
                            <p>Tiempo: <?php echo rand(10, 30); ?> min</p> 
                            
                            <p style="font-size: 0.8em; color: #777;">
                                <?php echo htmlspecialchars($platillo['ingredientes']); ?>
                            </p>

                            <form action="../api/agregar_al_plan.php" method="POST" style="margin-top: 10px;">
                                
                                <input type="hidden" name="platillo_id" value="<?php echo $platillo['id']; ?>">
                                
                                <div style="display: flex; gap: 5px; margin-bottom: 8px;">
                                    <input type="date" name="fecha" required 
                                        value="<?php echo date('Y-m-d'); ?>" 
                                        style="width: 60%; padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                                        
                                    <select name="tiempo" required style="width: 40%; padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                                        <option value="Desayuno">Desayuno</option>
                                        <option value="Almuerzo">Almuerzo</option>
                                        <option value="Cena">Cena</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn" style="width: 100%;">Usar sugerencia</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p style="text-align: center; width: 100%;">No hay platillos en esta categoría aún :(</p>
            <?php endif; ?>

        </section>

    </main>

</body>
</html>