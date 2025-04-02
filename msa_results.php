<?php
include 'config.php';
session_start();

// Check if the form was submitted and if proteins are selected
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_proteins'])) {
    $selected_proteins = $_POST['selected_proteins'];

    if (count($selected_proteins) < 2) {
        die("Error: At least two proteins are required for MSA.");
    }

    // Get the sequences of the selected proteins
    $placeholders = implode(',', array_fill(0, count($selected_proteins), '?'));
    $stmt = $pdo->prepare("SELECT protein_id, sequence FROM protein_sequences WHERE protein_id IN ($placeholders)");
    $stmt->execute($selected_proteins);
    $proteins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($proteins)) {
        die("Error: No proteins found.");
    }

    // Prepare the sequences for Clustal Omega input
    $sequences = '';
    foreach ($proteins as $protein) {
        $sequences .= ">{$protein['protein_id']}\n{$protein['sequence']}\n";
    }

    $tmpFile = tempnam(sys_get_temp_dir(), 'clustal_');
    file_put_contents($tmpFile, $sequences);

    // Call Clustal Omega for MSA (example command line, adjust as necessary)
    $outputFile = tempnam(sys_get_temp_dir(), 'msa_result_');
    $command = "clustalo -i $tmpFile -o $outputFile --force --outfmt=clustal";
    exec($command);

    $msaResult = file_get_contents($outputFile);

    // Display the result
    echo "<div class='container'>";
    echo "<h2 class='result-header'>Clustal Omega MSA Results</h2>";
    echo "<pre class='msa-result'>$msaResult</pre>";
    echo "</div>";

    unlink($tmpFile);
    unlink($outputFile);
} else {
    echo "<div class='container'>";
    echo "<h2 class='error-message'>No proteins selected for MSA.</h2>";
    echo "</div>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #2c3e50;
        padding: 15px 0;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2.result-header {
        font-size: 2em;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
    }

    h2.error-message {
        font-size: 1.8em;
        color: #e74c3c;
        text-align: center;
        margin-bottom: 20px;
    }

    pre.msa-result {
        font-family: "Courier New", Courier, monospace;
        background-color: #ecf0f1;
        padding: 20px;
        border-radius: 5px;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 1em;
        overflow-x: auto;
    }
</style>
