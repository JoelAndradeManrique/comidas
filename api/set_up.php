<?php
// api/crear_admin.php
require_once '../config/db.php'; // La ruta correcta estando dentro de 'api'

// Datos de TU usuario Admin (Joel)
$nombre = 'Joel Andrade';
$email = 'andrademanrique38@gmail.com';
$password_plano = 'Andrade21'; 
$rol = 'ADMIN';

// 1. Hasheamos la contraseña (Esto genera un string largo y seguro tipo $2y$10$...)
$password_hash = password_hash($password_plano, PASSWORD_DEFAULT);

try {
    // Verificamos si ya existe para no duplicarlo
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->execute([$email]);
    
    if($check->rowCount() > 0){
        echo "<h1>Aviso</h1><p>El usuario $email ya existe en la base de datos.</p>";
    } else {
        // 2. Preparamos la inserción
        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :pass, :rol)";
        $stmt = $pdo->prepare($sql);
        
        // 3. Ejecutamos
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':pass' => $password_hash, // ¡Aquí va el hash!
            ':rol' => $rol
        ]);

        echo "<h1>¡Usuario Creado con Éxito!</h1>";
        echo "<ul>
                <li>Usuario: <strong>$nombre</strong></li>
                <li>Email: <strong>$email</strong></li>
                <li>Password: <strong>$password_plano</strong> (En BD se guardó encriptada)</li>
              </ul>";
        echo "<p style='color:red'>Por seguridad, borra este archivo después de usarlo.</p>";
    }

} catch (PDOException $e) {
    echo "Error fatal: " . $e->getMessage();
}
?>