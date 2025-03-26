<?php
require 'config.php'; // Database configuration file

// Get protein_id from URL parameter
if (!isset($_GET['protein_id'])) {
    die("Protein ID is required.");
}
$protein_id = $_GET['protein_id'];

// Check if the protein_id exists in the database
$sql = "SELECT * FROM protein_analysis WHERE protein_id = :protein_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['protein_id' => $protein_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    // If protein_id exists, display the results
    echo "<h1>Protein Analysis for ID: {$protein_id}</h1>";
    echo "<p>Protein ID: " . $result['protein_id'] . "</p>";
    echo "<p>Sequence: <pre>" . nl2br(htmlspecialchars_decode($result['sequence'])) . "</pre></p>";
    echo "<p>Motif Results: <pre>" . nl2br(htmlspecialchars_decode($result['motif_results'])) . "</pre></p>";
    echo "<p>Property Results: <pre>" . nl2br(htmlspecialchars_decode($result['property_results'])) . "</pre></p>";
    echo "<p>Secondary Structure Results: <pre>" . nl2br(htmlspecialchars_decode($result['structure_results'])) . "</pre></p>";
} else {
    // If protein_id does not exist, retrieve the sequence and run Python scripts
    $stmt = $pdo->prepare("SELECT sequence FROM protein_sequences WHERE protein_id = :protein_id");
    $stmt->execute(['protein_id' => $protein_id]);
    $sequence_result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sequence_result) {
        $sequence = $sequence_result['sequence'];

        // Run motif_analysis.py
        $motif_output = shell_exec("python3 motif_analysis.py $protein_id '$sequence'");
        $motif_results_file = 'motif_results/' . $protein_id . '_motif.txt';
        if (file_exists($motif_results_file)) {
            $motif_results = file_get_contents($motif_results_file);
            $motif_results = htmlspecialchars($motif_results, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        } else {
            $motif_results = null;
        }

        // Run property.py
        $property_output = shell_exec("python3 property.py $protein_id '$sequence'");
        $property_results_file = 'property_results/' . $protein_id . '_property.txt';
        if (file_exists($property_results_file)) {
            $property_results = file_get_contents($property_results_file);
            $property_results = htmlspecialchars($property_results, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        } else {
            $property_results = null;
        }

        // Run sec_structure.py
        $structure_output = shell_exec("python3 sec_structure.py $protein_id '$sequence'");
        $structure_results_file = 'structure_results/' . $protein_id . '_structure.txt';
        if (file_exists($structure_results_file)) {
            $structure_results = file_get_contents($structure_results_file);
            $structure_results = htmlspecialchars($structure_results, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        } else {
            $structure_results = null;
        }

        // Save results into the database
        $insert_sql = "INSERT INTO protein_analysis (protein_id, sequence, motif_results, property_results, structure_results) 
                       VALUES (:protein_id, :sequence, :motif_results, :property_results, :structure_results)";
        $stmt = $pdo->prepare($insert_sql);
        $stmt->execute([
            'protein_id' => $protein_id,
            'sequence' => $sequence,
            'motif_results' => $motif_results,
            'property_results' => $property_results,
            'structure_results' => $structure_results
        ]);

        // Display the results
        echo "<h1>Protein Analysis for ID: {$protein_id}</h1>";
        echo "<p>Protein ID: {$protein_id}</p>";
        echo "<p>Sequence: {$sequence}</p>";
        echo "<h3>Motif Results:</h3><pre>{$motif_results}</pre>";
        echo "<h3>Property Results:</h3><pre>{$property_results}</pre>";
        echo "<h3>Secondary Structure Results:</h3><pre>{$structure_results}</pre>";
    } else {
        echo "<p>Protein sequence not found for Protein ID: {$protein_id}</p>";
    }
}
?>
