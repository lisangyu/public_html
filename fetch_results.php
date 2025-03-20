<?php
require "config.php"; // Connect to database

header("Content-Type: application/json");

if (!isset($_GET['search_id'])) {
    echo json_encode(["error" => "No search ID provided."]);
    exit;
}

$search_id = $_GET['search_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM results WHERE search_id = :search_id");
    $stmt->execute(["search_id" => $search_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            "emboss_result"   => $result["emboss_result"] ?? null,
            "clustalo_result" => $result["clustalo_result"] ?? null,
            "blast_result"    => $result["blast_result"] ?? null,
            "plots_result"    => $result["plots_result"] ? "plots/" . $result["plots_result"] : null,
            "motifs_result"   => $result["motifs_result"] ?? null
        ]);
    } else {
        echo json_encode(["error" => "No results found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
