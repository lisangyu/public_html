<?php
session_start();
require 'config.php'; // Connect to database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query using PDO syntax
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);

    if ($stmt->rowCount() > 0) {
        // Fetch the hashed password from the database
        $hashed_password = $stmt->fetchColumn();
        
        // Verify the entered password against the stored hashed password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username; // Store session
            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        
        <label>Password:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
    
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    
    <a href="register.php">Don't have an account? Register here.</a>
</body>
</html>
