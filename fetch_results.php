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
        // If a result is found, display the protein information
        echo "<h2>Protein Analysis Result</h2>";
        echo "Protein ID: " . htmlspecialchars($result['protein_id']) . "<br>";
        echo "Analysis Result: " . htmlspecialchars($result['result_data']) . "<br>";
        echo "Date: " . htmlspecialchars($result['created_at']) . "<br>";
    } else {
        // If no result is found, display a message
        echo "<h2>No Results Found</h2>";
        echo "<p>We couldn't find any results for the given ID. Please try again with a valid result ID.</p>";
    }
} else {
    // If no result_id is provided in the query parameters, display all results
    $stmt = $pdo->prepare("SELECT * FROM results ORDER BY created_at DESC");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        echo "<h2>Historical Query Results</h2>";
        foreach ($results as $result) {
            echo "<div class='result-item'>";
            echo "<h3>Protein ID: " . htmlspecialchars($result['protein_id']) . "</h3>";
            echo "<p>Analysis Result: " . htmlspecialchars($result['result_data']) . "</p>";
            echo "<p>Date: " . htmlspecialchars($result['created_at']) . "</p>";
            echo "</div>";
        }
    } else {
        // If there are no historical results, display a message
        echo "<h2>No Historical Results</h2>";
        echo "<p>No previous queries were found. Please submit a new query to generate results.</p>";
    }
}
?>
