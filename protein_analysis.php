<?php
require 'config.php'; // Database configuration file

// Get protein_id from URL parameter
if (!isset($_GET['protein_id'])) {
    die("<div class='error-message'>Protein ID is required.</div>");
}
$protein_id = $_GET['protein_id'];

// Check if the protein_id exists in the database
$sql = "SELECT * FROM protein_analysis WHERE protein_id = :protein_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['protein_id' => $protein_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<div class='container'>";

if ($result) {
    // If protein_id exists, display the results
    echo "<h1 class='heading'>Protein Analysis for ID: {$protein_id}</h1>";
    echo "<div class='result-section'>";
    echo "<h2>Protein Information</h2>";
    echo "<p><strong>Protein ID:</strong> " . $result['protein_id'] . "</p>";
    echo "<p><strong>Sequence:</strong> <pre>" . nl2br(htmlspecialchars_decode($result['sequence'])) . "</pre></p>";
    echo "<p><strong>Motif Results:</strong> <pre>" . nl2br(htmlspecialchars_decode($result['motif_results'])) . "</pre></p>";
    echo "<p><strong>Property Results:</strong> <pre>" . nl2br(htmlspecialchars_decode($result['property_results'])) . "</pre></p>";
    echo "<p><strong>Secondary Structure Results:</strong> <pre>" . nl2br(htmlspecialchars_decode($result['structure_results'])) . "</pre></p>";
    echo "</div>";
    echo "<div class='download-links'>";
    echo "<a href='motif_results/{$protein_id}_motif.txt' download='{$protein_id}_motif.txt' class='download-link'>Download Motif Result File</a><br>";
    echo "<a href='property_results/{$protein_id}_property.txt' download='{$protein_id}_property.txt' class='download-link'>Download Property Result File</a><br>";
    echo "<a href='structure_results/{$protein_id}_structure.txt' download='{$protein_id}_structure.txt' class='download-link'>Download Secondary Structure Result File</a>";
    echo "</div>";
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
        echo "<h1 class='heading'>Protein Analysis for ID: {$protein_id}</h1>";
        echo "<div class='result-section'>";
        echo "<h2>Protein Information</h2>";
        echo "<p><strong>Protein ID:</strong> {$protein_id}</p>";
        echo "<p><strong>Sequence:</strong> <pre>{$sequence}</pre></p>";
        echo "<h3>Motif Results:</h3><pre>{$motif_results}</pre>";
        echo "<h3>Property Results:</h3><pre>{$property_results}</pre>";
        echo "<h3>Secondary Structure Results:</h3><pre>{$structure_results}</pre>";
        echo "</div>";
        
        echo "<div class='download-links'>";
        echo "<a href='motif_results/{$protein_id}_motif.txt' download='{$protein_id}_motif.txt' class='download-link'>Download Motif Result File</a><br>";
        echo "<a href='property_results/{$protein_id}_property.txt' download='{$protein_id}_property.txt' class='download-link'>Download Property Result File</a>";
        echo "<a href='structure_results/{$protein_id}_structure.txt' download='{$protein_id}_structure.txt' class='download-link'>Download Secondary Structure Result File</a>";
        echo "</div>";
    } else {
        echo "<div class='error-message'>Protein sequence not found for Protein ID: {$protein_id}</div>";
    }
}

echo "</div>";
?>

<!-- Add CSS for better styling -->
<style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .container {
        width: 80%;
        margin: 40px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .heading {
        font-size: 2.5em;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 20px;
    }

    .result-section {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .result-section h2, .result-section h3 {
        color: #2980b9;
    }

    .result-section p, .result-section pre {
        font-size: 1.1em;
        color: #333;
    }

    .result-section pre {
        background-color: #ecf0f1;
        padding: 15px;
        border-radius: 5px;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-family: "Courier New", Courier, monospace;
        font-size: 1.1em;
    }

    .error-message {
        background-color: #e74c3c;
        color: white;
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .error-message a {
        color: white;
        text-decoration: underline;
    }

    .download-links {
    margin-top: 20px;
    padding: 20px;
    background-color: #ecf0f1;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    }

    .download-links a {
        display: inline-block;
        margin: 10px 15px;
        padding: 12px 20px;
        background-color: #2980b9;
        color: white;
        text-decoration: none;
        font-size: 1.2em;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .download-links a:hover {
        background-color: #3498db;
        transform: translateY(-3px);
    }

    .download-links a:active {
        transform: translateY(0);
    }

    .download-links .error-message {
        color: #e74c3c;
        font-weight: bold;
        margin-top: 10px;
        font-size: 1.1em;
    }
</style>
