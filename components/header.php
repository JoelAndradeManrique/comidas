<?php
// Aseguramos que no haya error si session_start ya fue llamado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. OBTENER DATOS DEL USUARIO
$nombre_usuario = $_SESSION['user_name'] ?? 'Usuario';
$rol_usuario = $_SESSION['user_role'] ?? 'CLIENTE';

// 2. GENERAR INICIALES (Ej: "Joel Andrade" -> "JA")
$partes_nombre = explode(' ', $nombre_usuario);
$iniciales = strtoupper(substr($partes_nombre[0], 0, 1)); // Primera letra del primer nombre
if (isset($partes_nombre[1])) {
    $iniciales .= strtoupper(substr($partes_nombre[1], 0, 1)); // Primera letra del segundo nombre
}

// 3. DETECTAR PÁGINA ACTUAL (Para marcar el link activo)
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<header class="main-header">
    <link rel="stylesheet" href="../css/header.css" />

    <div class="logo">
        <h2>MyFoods</h2>
    </div>

    <nav class="menu">
        <a href="seleccion_comidas.php" class="<?php echo $pagina_actual == 'seleccion_comidas.php' ? 'active' : ''; ?>">Comidas</a>
        
        <a href="sugerencias.php" class="<?php echo $pagina_actual == 'sugerencias.php' ? 'active' : ''; ?>">Sugerencias</a>
        
        <a href="plan.php" class="<?php echo $pagina_actual == 'plan.php' ? 'active' : ''; ?>">Plan</a>
        
        <?php if ($rol_usuario === 'ADMIN'): ?>
            <a href="editor_comidas.php" class="<?php echo $pagina_actual == 'editor_comidas.php' ? 'active' : ''; ?>">Editor</a>
        <?php endif; ?>
        
        <a href="ingredientes.php" class="<?php echo $pagina_actual == 'ingredientes.php' ? 'active' : ''; ?>">Ingredientes</a>
    </nav>

    <div class="user-section">
        <div class="user-initials" id="userBtn" title="<?php echo htmlspecialchars($nombre_usuario); ?>">
            <?php echo $iniciales; ?>
        </div>

        <div class="dropdown" id="dropdownMenu">
            <div class="user-info-dropdown">
                Hola, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>
            </div>
            <a href="../api/logout.php" class="logout-link">Cerrar sesión</a>
        </div>
    </div>

</header>

<style>
    /* Agregamos estilo para el link activo */
    .menu a.active {
        border-bottom: 2px solid #fff;
        font-weight: bold;
    }
    .user-info-dropdown {
        padding: 10px;
        border-bottom: 1px solid #eee;
        font-size: 0.9em;
        color: #333;
        text-align: center;
    }
    .logout-link {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #d9534f; /* Rojo suave */
        text-align: center;
        width: 100%;
        background: none;
        border: none;
        cursor: pointer;
    }
    .logout-link:hover {
        background-color: #f8f9fa;
        color: #c9302c;
    }
</style>

<script>
    const userBtn = document.getElementById("userBtn");
    const dropdown = document.getElementById("dropdownMenu");

    userBtn.addEventListener("click", (e) => {
        e.stopPropagation(); // Evita que el click se propague al document
        dropdown.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
        if (!dropdown.contains(e.target) && e.target !== userBtn) {
            dropdown.classList.remove("show");
        }
    });
</script>