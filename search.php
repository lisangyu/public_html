<?php
// search.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input
    $protein_family = $_POST['protein-family'];
    $taxonomy = $_POST['taxonomy'];

    // Generate a unique search ID for this query
    $search_id = uniqid('search_', true);

    // Call backend scripts to fetch data and perform analysis
    // Fetch protein data
    $command = escapeshellcmd("bash fetch_protein_data.sh '$protein_family' '$taxonomy' '$search_id'");
    shell_exec($command . " > /dev/null 2>&1 &");

    // Return json response
    echo json_encode(["status" => "success", "search_id" => $search_id]);
    exit;
}
?>
