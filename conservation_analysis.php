<?php
include 'config.php'; // Include database connection

// Get search_id from request
$search_id = $_GET['search_id'] ?? '';

if (empty($search_id)) {
    die("Error: search_id is required.");
}

try {
    // 1. Check if Clustal Omega results already exist in the database
    $sql = "SELECT alignment_content, plot_image FROM conservation_results WHERE search_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$search_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // If results exist, use stored values
        $alignment_content = $result['alignment_content'];
        $plot_image = $result['plot_image'];
    } else {
        // 2. Retrieve protein sequences associated with search_id
        $sql = "SELECT ps.protein_id, ps.sequence 
                FROM search_protein sp 
                JOIN protein_sequences ps ON sp.protein_id = ps.protein_id 
                WHERE sp.search_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_id]);
        $proteins = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if (empty($proteins)) {
            die("Error: No protein sequences found for this search_id.");
        }

        // Create temporary FASTA file
        //echo "Step 2: Creating a temporary FASTA file...\n";
        //$save_dir = "/localdisk/home/s2746775/public_html/website/tmp_files/";
        //if (!file_exists($save_dir)) {
        //    mkdir($save_dir, 0777, true);
        //}
        //$temp_file = $save_dir . "seq_" . uniqid() . ".fasta";
        //$fasta_content = "";
        //foreach ($proteins as $protein) {
        //    $fasta_content .= "> " . htmlspecialchars($protein['protein_id']) . "\n" . htmlspecialchars($protein['sequence']) . "\n";
        //}
        //if (file_put_contents($temp_file, $fasta_content) === false) {
        //    die("Error: Unable to write to $temp_file.");
        //}
        
        //echo "FASTA file created: $temp_file\n";

        // Output the content of the temporary FASTA file for debugging
        //echo "Temporary FASTA file content:\n";
        //echo file_get_contents($temp_file);  // Show the content of the temporary file

        // Run the Python conservation analysis script
        $temp_file ="/home/s2746775/public_html/website/protein_results/$search_id.fasta";
        //echo "Step 3: Running Python script for conservation analysis...\n";
        $command = "/usr/bin/python3 /home/s2746775/public_html/website/conservation.py $temp_file $search_id";
        //echo "Running command: $command\n";
        //exec("ls -l " . escapeshellarg($temp_file));
        //exec($command, $output, $status);
        
        // Check if Python script executed successfully
        //if ($status !== 0) {
        //    echo "Python script failed. Status code: $status\n";
        //    echo "Output from Python script: \n" . implode("\n", $output) . "\n";
        //    die("Error: Conservation analysis failed.");
        //}
        //echo "Python script executed successfully.\n";
        
        // Execute the command and capture the output
        $output = shell_exec($command);

        // Check if the command executed successfully
        //if ($output === null) {
        //    echo "Error: Python script execution failed.\n";
        //} else {
        //    echo "Python script output:\n" . $output . "\n";
        //}

        // Clean up the temporary file
        //unlink($temp_file);
        //echo "Temporary file deleted.\n";

        // Retrieve output file paths from Python script
        $plot_image = "conservation_results/".$search_id."_plotcon.png";
        $alignment_file = "conservation_results/" . $search_id . ".aln";
        $alignment_content = file_get_contents($alignment_file);

        // 5. Store results in the database
        $sql = "INSERT INTO conservation_results (search_id, alignment_content, plot_image) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_id, $alignment_content, $plot_image]);
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conservation Analysis Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h3 {
            color: #333;
        }
        pre {
            background-color: #fff;
            padding: 10px;
            border: 1px solid #ccc;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h3>Clustal Omega Alignment</h3>
    <pre><?php echo htmlspecialchars($alignment_content); ?></pre>

    <h3>Conservation Analysis</h3>
    <img src="<?php echo htmlspecialchars($plot_image); ?>" alt="Conservation Plot">
</body>
</html>
