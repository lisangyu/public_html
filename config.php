<?php
// config.php
$host = 'localhost';   // MySQL server address
$dbname = 'bioinformatics';  // Database name
$username = 'root';    // MySQL username
$password = '';        // MySQL password

try {
    // Establishing PDO connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
