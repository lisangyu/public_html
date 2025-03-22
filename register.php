<?php
session_start();
require 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT username FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);

    if ($stmt->rowCount() > 0) {
        $error = "Username already taken.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare the statement to insert the new user
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        
        // Execute the query with bound parameters
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password
        ]);

        // Automatically log in after registration
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Register</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        
        <label>Password:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Register</button>
    </form>
    
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    
    <a href="login.php">Already have an account? Login here.</a>
</body>
</html>
