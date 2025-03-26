<?php
session_start();
$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

if (empty($search_id)) {
    echo "Error: search_id is missing.";
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

// Run Clustalo command
$clustalo_command = "/usr/bin/clustalo -i $fasta_file -o $clustalo_file --force --outfmt=clustal";
exec($clustalo_command, $output, $status);

// Check if Clustalo executed successfully
//if ($status !== 0) {
//    echo "Error: Clustalo analysis failed.";
//    exit();
//}

// Run Plotcon command
$plotcon_command = "/usr/bin/plotcon -sequence $clustalo_file -graph png -winsize 4";
exec($plotcon_command, $output, $status);

// Check if Plotcon executed successfully
if ($status !== 0) {
    echo "Error: Plotcon failed to generate the image.";
    exit();
}

// Move and rename the Plotcon output
if (file_exists($default_plotcon_file)) {
    // Rename the file to include the search_id
    if (rename($default_plotcon_file, $plotcon_file)) {
        echo "Plotcon image generated successfully.<br>";
    } else {
        echo "Error: Failed to move the Plotcon image.";
        exit();
    }
} else {
    echo "Error: Plotcon did not generate the expected output.";
    exit();
}

// Display the results
echo "<h2>Clustalo Alignment Result</h2>";
echo "<pre>";
echo htmlspecialchars(file_get_contents($clustalo_file)); // Show the alignment in text format
echo "</pre>";

echo "<h2>Conservation Plot</h2>";
echo "<img src='conservation_results/{$search_id}_plotcon.png' alt='Plotcon Image' />";
?>
