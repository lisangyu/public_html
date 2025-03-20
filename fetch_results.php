<?php
// fetch_results.php
include('config.php');

// Get requested result ID from the query parameters
if (isset($_GET['result_id'])) {
    $result_id = $_GET['result_id'];

    // Query historical data from the database
    $stmt = $pdo->prepare("SELECT * FROM results WHERE id = :id");
    $stmt->execute(['id' => $result_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "Protein ID: " . htmlspecialchars($result['protein_id']) . "<br>";
        echo "Analysis Result: " . htmlspecialchars($result['result_data']) . "<br>";
    } else {
        echo "No results found for the given ID.";
    }
}
?>
