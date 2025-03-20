<?php
// search.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input
    $protein_family = $_POST['protein-family'];
    $taxonomy = $_POST['taxonomy'];

    // Generate a unique search ID for this query
    $search_id = uniqid('search_', true);

    // Call backend scripts to fetch data and perform analysis
    // 1. Fetch protein data
    shell_exec("bash fetch_protein_data.sh $protein_family $taxonomy");

    // 2. Perform EMBOSS, Clustal Omega, BLAST, and Motif analysis
    shell_exec("bash run_emboss_analysis.sh $protein_family $taxonomy");
    shell_exec("bash run_clustalo.sh $protein_family $taxonomy");
    shell_exec("bash run_blast.sh $protein_family $taxonomy");
    shell_exec("python3 generate_plots.py $protein_family $taxonomy");
    shell_exec("python3 process_motifs.py $protein_family $taxonomy");

    // 3. Store the results in the database
    shell_exec("php store_results.php $search_id");

    // 4. Redirect to results page with the search_id in the URL
    header("Location: results.html?search_id=$search_id");
    exit;
}
?>
