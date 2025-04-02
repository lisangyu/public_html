<?php
require 'config.php';

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
    // If exists, retrieve the BLASTP result stored as JSON
    $blast_data = json_decode($result['blastp_result'], true);
} else {
    try {
        // Retrieve sequence from the database if protein_id not found in protein_analysis table
        $stmt = $pdo->prepare("SELECT sequence FROM protein_sequences WHERE protein_id = :protein_id");
        $stmt->execute(['protein_id' => $protein_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            die("<p>Error: Protein ID not found.</p>");
        }

        $sequence = $result['sequence'];
    } catch (PDOException $e) {
        die("<p>Database error: " . $e->getMessage() . "</p>");
    }

    $blastp_output = "blastp_results/" . $protein_id . "_blastp.txt";
    
    $cmd = escapeshellcmd("python3 blast_analysis.py '$protein_id' '$sequence' '$blastp_output'");
    shell_exec($cmd);
    
    $blastp_result = file_get_contents($blastp_output);
    $blast_data = parse_blastp_results($blastp_result); // Parse the BLASTP result
    
    // Store BLASTP result as JSON in the database
    $json_blastp_result = json_encode($blast_data);
    $sql_insert = "INSERT INTO protein_analysis (protein_id, blastp_result) VALUES (:protein_id, :blastp_result)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute(['protein_id' => $protein_id, 'blastp_result' => $json_blastp_result]);
}

// Function to parse BLASTP result into an array
function parse_blastp_results($blastp_result) {
    $blast_data = [];
    $lines = explode("\n", trim($blastp_result));

    foreach ($lines as $line) {
        $columns = explode("\t", $line);
        if (count($columns) >= 12) {
            $blast_data[] = [
                'query_id' => $columns[0],
                'subject_id' => $columns[1],
                'percentage_identity' => $columns[2],
                'alignment_length' => $columns[3],
                'mismatches' => $columns[4],
                'gap_openings' => $columns[5],
                'q_start' => $columns[6],
                'q_end' => $columns[7],
                's_start' => $columns[8],
                's_end' => $columns[9],
                'e_value' => $columns[10],
                'bit_score' => $columns[11],
            ];
        }
    }
    return $blast_data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protein Analysis Result</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Protein Analysis Result</h2>
    <p><strong>Protein ID:</strong> <?php echo htmlspecialchars($protein_id); ?></p>
    
    <h3>BLASTP Result:</h3>
    <?php if (!empty($blast_data)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Query ID</th>
                    <th>Subject ID</th>
                    <th>Percentage Identity</th>
                    <th>Alignment Length</th>
                    <th>Mismatches</th>
                    <th>Gap Openings</th>
                    <th>Query Start</th>
                    <th>Query End</th>
                    <th>Subject Start</th>
                    <th>Subject End</th>
                    <th>E-value</th>
                    <th>Bit Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blast_data as $data) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['query_id']); ?></td>
                        <td><?php echo htmlspecialchars($data['subject_id']); ?></td>
                        <td><?php echo htmlspecialchars($data['percentage_identity']); ?></td>
                        <td><?php echo htmlspecialchars($data['alignment_length']); ?></td>
                        <td><?php echo htmlspecialchars($data['mismatches']); ?></td>
                        <td><?php echo htmlspecialchars($data['gap_openings']); ?></td>
                        <td><?php echo htmlspecialchars($data['q_start']); ?></td>
                        <td><?php echo htmlspecialchars($data['q_end']); ?></td>
                        <td><?php echo htmlspecialchars($data['s_start']); ?></td>
                        <td><?php echo htmlspecialchars($data['s_end']); ?></td>
                        <td><?php echo htmlspecialchars($data['e_value']); ?></td>
                        <td><?php echo htmlspecialchars($data['bit_score']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No BLASTP results found for this protein.</p>
    <?php endif; ?>
</body>
</html>
