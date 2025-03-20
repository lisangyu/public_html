<?php
// Include the config file to establish the database connection
include 'config.php';

// Retrieve the search_id and protein_ids from the bash script via POST request
$search_id = $_POST['search_id'];
$protein_ids = $_POST['protein_ids'];

// Ensure that both search_id and protein_ids are provided
if (empty($search_id) || empty($protein_ids)) {
    die("Error: Missing search ID or protein IDs.");
}

// Split protein_ids string into an array
$protein_ids_array = explode(" ", $protein_ids);

// Insert each protein ID into the database
foreach ($protein_ids_array as $protein_id) {
    $sql = "INSERT INTO searches (search_id, protein_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_id, $protein_id);
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

echo "Protein IDs have been stored in the database.";
?>
