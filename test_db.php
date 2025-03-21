<?php
include 'config.php';

if ($pdo) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?>
