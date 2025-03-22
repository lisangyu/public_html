<?php
include 'config.php'; // Database connection
session_start();

// Set items per page
$limit = 20;

// Get search_id from GET parameter
$search_id = $_GET['search_id'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

if (empty($search_id)) {
    die("Error: search_id is required.");
}

// Get total count of results for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM protein_sequences WHERE search_id = ?");
$stmt->execute([$search_id]);
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch protein sequences for the given search_id with pagination
$sql = "SELECT protein_id, fasta_sequence FROM protein_sequences WHERE search_id = ? LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute([$search_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            color: #007bff;
            background-color: #f9f9f9;
        }
        .pagination a.active {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h2>Search Results for <?= htmlspecialchars($search_id) ?></h2>

    <?php if (count($results) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Protein ID</th>
                    <th>FASTA Sequence</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['protein_id']) ?></td>
                        <td style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($row['fasta_sequence'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search_id=<?= urlencode($search_id) ?>&page=<?= $page - 1 ?>">« Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?search_id=<?= urlencode($search_id) ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?search_id=<?= urlencode($search_id) ?>&page=<?= $page + 1 ?>">Next »</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No results found for this search.</p>
    <?php endif; ?>
</body>
</html>
