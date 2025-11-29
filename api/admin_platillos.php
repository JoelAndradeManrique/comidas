<?php
// api/admin_platillos.php
session_start();
require_once '../config/db.php';

// SEGURIDAD: Solo el ADMIN puede entrar
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    die("Acceso denegado.");
}

// 1. ELIMINAR
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Opcional: Podríamos borrar también la foto de la carpeta 'uploads', 
    // pero por ahora lo dejamos simple.
    $stmt = $pdo->prepare("DELETE FROM platillos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: ../views/editor_comidas.php');
    exit;
}

// 2. CREAR O EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $ingredientes = $_POST['ingredientes'];
    
    // --- LÓGICA DE IMAGEN ---
    // Por defecto, tomamos lo que haya en el input de texto (URL o la anterior)
    $imagen_final = $_POST['imagen_url']; 

    // Verificamos si se SUBIÓ un archivo nuevo
    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        
        $nombre_archivo = $_FILES['imagen_archivo']['name'];
        $tmp_name = $_FILES['imagen_archivo']['tmp_name'];
        
        // 1. VALIDACIÓN DE EXTENSIÓN (SEGURIDAD)
        $ext = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION)); // Obtenemos la extensión en minúsculas
        $permitidos = ['png', 'jpg', 'jpeg'];
        
        // 2. VALIDACIÓN DE TIPO MIME (DOBLE SEGURIDAD)
        // Esto verifica que el contenido real del archivo sea una imagen
        $check_img = getimagesize($tmp_name);

        if (in_array($ext, $permitidos) && $check_img !== false) {
            
            // Si pasa las pruebas, guardamos
            $nuevo_nombre = time() . "_" . basename($nombre_archivo);
            $ruta_destino = "../uploads/" . $nuevo_nombre;

            if (move_uploaded_file($tmp_name, $ruta_destino)) {
                $imagen_final = $ruta_destino;
            }
        } else {
            // Si intenta subir otra cosa, detenemos todo con un error
            die("Error: Solo se permiten archivos PNG, JPG o JPEG válidos. <a href='../views/editor_comidas.php'>Volver</a>");
        }
    }

    try {
        if ($id) {
            // EDITAR
            $sql = "UPDATE platillos SET nombre=?, categoria=?, ingredientes=?, imagen_url=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $categoria, $ingredientes, $imagen_final, $id]);
        } else {
            // CREAR
            $sql = "INSERT INTO platillos (nombre, categoria, ingredientes, imagen_url) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $categoria, $ingredientes, $imagen_final]);
        }
        
        header('Location: ../views/editor_comidas.php');

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>