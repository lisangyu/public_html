<?php
// store_results.php
include('config.php');

// Assume Python or Bash script generates a result file, e.g., result.txt
$result_file = 'result.txt';  // Assume this file exists and contains the analysis result

// Read the result file
if (file_exists($result_file)) {
    $result_data = file_get_contents($result_file);

    // Store the result in the database
    $stmt = $pdo->prepare("INSERT INTO results (protein_id, result_data) VALUES (:protein_id, :result_data)");
    $stmt->execute([
        'protein_id' => 'SampleProteinID',  // Protein ID should be dynamically obtained
        'result_data' => $result_data
    ]);

    echo "Results stored successfully.";
} else {
    echo "Error: Result file not found.";
}
?>
