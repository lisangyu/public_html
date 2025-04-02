<?php

include 'config.php';
session_start();

// Set response header to return JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $protein_family = $_POST['protein-family'] ?? '';
    $taxonomy = $_POST['taxonomy'] ?? '';

    if (empty($protein_family) || empty($taxonomy)) {
        echo json_encode(["status" => "error", "message" => "Protein family and taxonomy are required"]);
        exit;
    }

    // Generate a unique search ID for this query
    $search_id = uniqid('search_', true);

    $command = escapeshellcmd("bash fetch_protein_data.sh '$protein_family' '$taxonomy' '$search_id'");
    $output = shell_exec($command);

    if (empty($output)) {
        echo json_encode(["status" => "error", "message" => "No protein data fetched"]);
        exit;
    }

    // Split the output string into an array of protein IDs
    $protein_ids_array = explode("\n", trim($output));

    try {
        $pdo->beginTransaction();

        // If the user is logged in, store search history
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = $pdo->prepare("INSERT INTO search_history (search_id, username, Protein_Family, Taxonomy) VALUES (?, ?, ?, ?)");
            $stmt->execute([$search_id, $username, $protein_family, $taxonomy]);
        }

        $fasta_file = "protein_results/{$search_id}.fasta";

        if (!file_exists($fasta_file)) {
            throw new Exception("No protein data fetched");
        }

        $fasta_contents = file_get_contents($fasta_file);

        preg_match_all("/>(\S+)\s*(.*?)\n([^>]*)/s", $fasta_contents, $matches, PREG_SET_ORDER);

        // Create a dictionary mapping accession numbers to their corresponding FASTA sequences
        $fasta_dict = [];
        foreach ($matches as $match) {
            $acc_number = trim($match[1]);
            $header = trim($match[2]);

            $sequence = preg_replace('/\s+/', '', trim($match[3]));

            // Extract protein name and species from header (assumes a specific format)
            preg_match("/\[(.*?)\]/", $header, $species_match);
            $species = $species_match[1] ?? '';
            $protein_name = preg_replace("/\s*\[.*\]/", '', $header);

            $fasta_dict[$acc_number] = [
                'sequence' => $sequence,
                'protein_name' => $protein_name,
                'species' => $species
            ];
        }

        $sql_insert_protein = "INSERT IGNORE INTO protein_sequences (protein_id, protein_name, species, sequence) VALUES (?, ?, ?, ?)";
        $stmt_insert_protein = $pdo->prepare($sql_insert_protein);

        $sql_insert_search = "INSERT INTO search_protein (search_id, protein_id) VALUES (?, ?)";
        $stmt_insert_search = $pdo->prepare($sql_insert_search);

        // Iterate through the protein IDs obtained from the shell script output
        foreach ($protein_ids_array as $protein_id) {
            if (isset($fasta_dict[$protein_id])) {
                $stmt_insert_protein->execute([
                    $protein_id,
                    $fasta_dict[$protein_id]['protein_name'],
                    $fasta_dict[$protein_id]['species'],
                    $fasta_dict[$protein_id]['sequence']
                ]);
            }

            $stmt_insert_search->execute([$search_id, $protein_id]);
        }

        $pdo->commit();

        // Return success response
        echo json_encode(["status" => "success", "search_id" => $search_id]);

    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
