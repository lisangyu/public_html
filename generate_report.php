<?php
// generate_report.php
include('config.php');

// Check if result_id is provided in the query parameters
if (isset($_GET['result_id'])) {
    $result_id = $_GET['result_id'];

    // Fetch the analysis result from the database
    $stmt = $pdo->prepare("SELECT * FROM results WHERE id = :id");
    $stmt->execute(['id' => $result_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Set CSV headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="analysis_result.csv"');

        // Open output stream for writing CSV
        $output = fopen('php://output', 'w');

        // Write CSV header
        fputcsv($output, ['Protein ID', 'Result Data']);

        // Write data to CSV
        fputcsv($output, [$result['protein_id'], $result['result_data']]);

        fclose($output);
    } else {
        echo "No results found for the given ID.";
    }
}
?>
