<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            // Insert new user into database
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute([':username' => $username, ':password' => $hashed_password]);

            // Automatically log the user in after registration
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
