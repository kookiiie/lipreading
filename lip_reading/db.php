<?php

ini_set('display_errors', 0); 
ini_set('memory_limit', '1024M'); 

$host = 'localhost';
$dbname = 'lipreading';
$username = 'root';
$password = '';
$port = '3306';


try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage()); 
    die("Database connection failed.");
}


if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true, 
        'secure' => true
    ]);
    session_start();
}


if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
session_regenerate_id(true); 

?>
