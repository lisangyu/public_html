<?php
session_start();
$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

if (empty($search_id)) {
    echo "<div class='error-message'>Error: search_id is missing.</div>";
    exit();
}

// Set paths for the files
$fasta_file = "/home/s2746775/public_html/website/protein_results/{$search_id}.fasta";
$clustalo_file = "/home/s2746775/public_html/website/conservation_results/{$search_id}.aln";
$default_plotcon_file = "/home/s2746775/public_html/website/plotcon.1.png";
$plotcon_file = "/home/s2746775/public_html/website/conservation_results/{$search_id}_plotcon.png";

// Ensure the conservation_results directory exists
if (!file_exists('/home/s2746775/public_html/website/conservation_results/')) {
    mkdir('/home/s2746775/public_html/website/conservation_results/', 0777, true);
}

// Check if the Clustalo file already exists
if (!file_exists($clustalo_file) || !file_exists($plotcon_file)) {
    // Run Clustalo command if the result does not exist
    $clustalo_command = "/usr/bin/clustalo -i $fasta_file -o $clustalo_file --force --outfmt=clustal";
    exec($clustalo_command, $output, $status);

    // Check if Clustalo executed successfully
    if ($status !== 0) {
        //echo "<div class='error-message'>Error: Clustalo analysis failed.</div>";
        exit();
    }

    // Run Plotcon command if the result does not exist
    $plotcon_command = "/usr/bin/plotcon -sequence $clustalo_file -graph png -winsize 4";
    exec($plotcon_command, $output, $status);

    // Check if Plotcon executed successfully
    if ($status !== 0) {
        //echo "<div class='error-message'>Error: Plotcon failed to generate the image.</div>";
        exit();
    }

    // Move and rename the Plotcon output
    if (file_exists($default_plotcon_file)) {
        // Rename the file to include the search_id
        if (rename($default_plotcon_file, $plotcon_file)) {
            //echo "<div class='success-message'>Plotcon image generated successfully.</div>";
        } else {
            //echo "<div class='error-message'>Error: Failed to move the Plotcon image.</div>";
            exit();
        }
    } else {
        echo "<div class='error-message'>Error: Plotcon did not generate the expected output.</div>";
        exit();
    }
} else {
    //echo "<div class='success-message'>Files already exist, displaying the results.</div>";
}

// Display the results
echo "<div class='container'>";
echo "<h2 class='result-header'>Clustalo Alignment Result</h2>";
echo "<pre class='alignment-result'>";
echo htmlspecialchars(file_get_contents($clustalo_file)); // Show the alignment in text format
echo "</pre>";

echo "<h2 class='result-header'>Conservation Plot</h2>";
echo "<div class='plot-container'>";
echo "<img src='conservation_results/{$search_id}_plotcon.png' alt='Plotcon Image' />";
echo "</div>";

// Add download links for the files
echo "<div class='download-links'>";
echo "<a href='conservation_results/{$search_id}.aln' download='{$search_id}.aln' class='download-link'>Download Clustalo Alignment File</a><br>";
echo "<a href='conservation_results/{$search_id}_plotcon.png' download='{$search_id}_plotcon.png' class='download-link'>Download Plotcon Image</a>";
echo "</div>";

echo "</div>";
?>

<!-- Add CSS for better styling -->
<style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
        color: #333;
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

    .alignment-result {
        font-family: "Courier New", Courier, monospace;
        background-color: #ecf0f1;
        padding: 20px;
        border-radius: 5px;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 1em;
        overflow-x: auto;
        margin-bottom: 20px;
    }

    .plot-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .plot-container img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .error-message, .success-message {
        padding: 10px;
        text-align: center;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .error-message {
        background-color: #e74c3c;
        color: white;
    }

    .success-message {
        background-color: #2ecc71;
        color: white;
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
