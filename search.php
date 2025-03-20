<?php
// search.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $protein_id = $_POST['protein_id'];  // Protein ID provided by the user

    // Call Python script to fetch protein data
    $command = "python3 fetch_protein_data.py $protein_id";  // Call the Python script
    $output = shell_exec($command);

    // Process output result
    if ($output) {
        echo "Protein data fetched successfully.<br>";
        // Display or store the result
    } else {
        echo "Failed to fetch protein data.<br>";
    }
}
?>

<form method="POST">
    Protein ID: <input type="text" name="protein_id" required>
    <input type="submit" value="Fetch Protein Data">
</form>
