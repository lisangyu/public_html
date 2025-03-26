<?php
include 'config.php'; // Database connection
session_start();

// Check if the form was submitted and if proteins are selected
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_proteins'])) {
    // Get selected protein IDs
    $selected_proteins = $_POST['selected_proteins'];
    
    // Ensure there are at least two proteins selected
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

    // Save the sequences to a temporary file
    $tmpFile = tempnam(sys_get_temp_dir(), 'clustal_');
    file_put_contents($tmpFile, $sequences);

    // Call Clustal Omega for MSA (example command line, adjust as necessary)
    $outputFile = tempnam(sys_get_temp_dir(), 'msa_result_');
    $command = "clustalo -i $tmpFile -o $outputFile --force --outfmt=clustal";
    exec($command);

    // Read the MSA result
    $msaResult = file_get_contents($outputFile);

    // Display the result (or save to file)
    echo "<h2>Clustal Omega MSA Results</h2>";
    echo "<pre>$msaResult</pre>";

    // Clean up temporary files
    unlink($tmpFile);
    unlink($outputFile);
} else {
    echo "No proteins selected for MSA.";
}
?>
