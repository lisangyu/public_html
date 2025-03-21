<?php
// Generate a unique search ID for this query
$search_id = uniqid('search_', true);

// Call backend scripts to fetch data and perform analysis
// Fetch protein data
$command = escapeshellcmd("bash fetch_protein_data.sh 'Actin' 'Mus musculus' '$search_id'");
$output = shell_exec($command);
//echo "Executing command: $command\n";
echo $output;
echo $search_id;
//$command1 = escapeshellcmd("which esearch");
//$output1 = shell_exec($command1);
//echo $output1;
?>
