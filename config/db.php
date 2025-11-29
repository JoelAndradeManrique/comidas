<?php
// config/db.php
$host = 'localhost';
$dbname = 'comidas';
$username = 'root'; // Cambia esto si tienes otro usuario
$password = '';     // Cambia esto si tienes contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Habilitar errores para poder depurar si algo falla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("¡Error fatal de conexión!: " . $e->getMessage());
}
?>