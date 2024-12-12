<?php
// Archivo de configuración para la base de datos
$host = 'localhost';
$dbname = 'pizzeria';
$username = 'root'; // Tu usuario de MySQL
$password = '12345'; // Tu contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
?>