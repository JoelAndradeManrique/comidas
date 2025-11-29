<?php
session_start();
require_once '../config/db.php';

// SEGURIDAD
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header('Location: plan.php');
    exit;
}

// LOGICA DE EDICIÓN
$platillo_editar = [
    'id' => '', 'nombre' => '', 'categoria' => 'General', 'ingredientes' => '', 'imagen_url' => ''
];

if (isset($_GET['editar_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM platillos WHERE id = ?");
    $stmt->execute([$_GET['editar_id']]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        $platillo_editar = $resultado;
    }
}

// LOGICA LISTADO
$lista_platillos = $pdo->query("SELECT * FROM platillos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Comidas (Admin)</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/editor_comidas.css">
</head>
<body>

    <?php include '../components/header.php'; ?>

    <main class="editor-container">

        <h2>Panel de Administración</h2>
        <p class="desc">Gestión del Catálogo General (Solo Admin)</p>

        <section class="editor-panel">

            <form class="food-form" action="../api/admin_platillos.php" method="POST" enctype="multipart/form-data">
                
                <h3><?php echo $platillo_editar['id'] ? '✏️ Editar Comida' : '➕ Crear Nueva Comida'; ?></h3>

                <input type="hidden" name="id" value="<?php echo $platillo_editar['id']; ?>">

                <label>Nombre de la comida</label>
                <input type="text" name="nombre" required 
                       value="<?php echo htmlspecialchars($platillo_editar['nombre']); ?>" 
                       placeholder="Ej. Ensalada con pollo">

                <label>Categoría</label>
                <select name="categoria">
                    <option value="General" <?php echo $platillo_editar['categoria']=='General'?'selected':''; ?>>General</option>
                    <option value="Rápido" <?php echo $platillo_editar['categoria']=='Rápido'?'selected':''; ?>>Rápido</option>
                    <option value="Saludable" <?php echo $platillo_editar['categoria']=='Saludable'?'selected':''; ?>>Saludable</option>
                    <option value="Económico" <?php echo $platillo_editar['categoria']=='Económico'?'selected':''; ?>>Económico</option>
                </select>

                <label>Imagen del Platillo</label>
                
                <div style="background: #f9f9f9; padding: 10px; border-radius: 5px; border: 1px dashed #ccc; margin-bottom: 10px;">
                    <p style="margin: 0 0 5px 0; font-size: 0.9em; color: #666;">📁 Subir archivo desde tu equipo:</p>
                    <input type="file" name="imagen_archivo" accept=".jpg, .jpeg, .png">
                </div>

                <p style="margin: 5px 0; font-size: 0.85em; color: #999; text-align: center;">--- O ---</p>
                
                <input type="text" name="imagen_url" 
                       value="<?php echo htmlspecialchars($platillo_editar['imagen_url']); ?>" 
                       placeholder="Pegar URL de internet (opcional)">
                
                <?php if($platillo_editar['imagen_url']): ?>
                    <p style="font-size: 0.8em; color: green; margin-top: -5px;">✔ Imagen actual registrada</p>
                <?php endif; ?>


                <label>Ingredientes</label>
                <textarea name="ingredientes" rows="4" required placeholder="Ej. pollo, lechuga..."><?php echo htmlspecialchars($platillo_editar['ingredientes']); ?></textarea>

                <button type="submit" class="btn-save">
                    <?php echo $platillo_editar['id'] ? 'Actualizar Comida' : 'Guardar Nueva'; ?>
                </button>
                
                <?php if($platillo_editar['id']): ?>
                    <a href="editor_comidas.php" style="display:block; text-align:center; margin-top:10px; color: red; text-decoration:none;">Cancelar Edición</a>
                <?php endif; ?>
            </form>

            <div class="food-list">
                <h3>Catálogo Actual (<?php echo count($lista_platillos); ?>)</h3>

                <?php foreach ($lista_platillos as $p): ?>
                    <div class="food-item">
                        <?php $thumb = !empty($p['imagen_url']) ? $p['imagen_url'] : 'https://via.placeholder.com/50'; ?>
                        <img src="<?php echo $thumb; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">

                        <div style="flex-grow: 1;">
                            <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                            <p style="font-size: 0.8em; color: #666;"><?php echo htmlspecialchars($p['categoria']); ?></p>
                        </div>
                        <div class="actions">
                            <a href="?editar_id=<?php echo $p['id']; ?>" class="edit">✏️</a>
                            
                            <a href="../api/admin_platillos.php?action=delete&id=<?php echo $p['id']; ?>" 
                               class="delete" 
                               onclick="return confirm('¿Borrar <?php echo htmlspecialchars($p['nombre']); ?>?');">
                               🗑️
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        </section>

    </main>

</body>
</html>