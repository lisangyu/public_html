<?php
// config.php
$host = '127.0.0.1';   // MySQL server address
$dbname = 's2746775';  // Database name
$username = 's2746775';    // MySQL username
//$password = getenv('DB_PASSWORD');        // MySQL password
$password = 'Qk3UxizAsaJO8ld_w6xrb1xdG2HRXoQpR$';

try {
    // Establishing PDO connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Return a JSON response with an error message if database connection fails
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit;  // Ensure no other output is sent
}
?>
