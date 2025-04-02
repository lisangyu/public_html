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
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
