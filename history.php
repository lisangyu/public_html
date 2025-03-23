<?php
// Include the database configuration file
include 'config.php';
session_start(); // Start session to check user login status

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Get the current page number from URL, default to page 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 20; // Number of records per page

// Calculate the offset for SQL query
$offset = ($page - 1) * $records_per_page;

try {
    // Get total number of search history records for the user
    $sql_total = "SELECT COUNT(*) FROM search_history WHERE username = ?";
    $stmt_total = $pdo->prepare($sql_total);
    $stmt_total->execute([$username]);
    $total_records = $stmt_total->fetchColumn(); // Fetch total number of records

    // Calculate total pages
    $total_pages = ($total_records > 0) ? ceil($total_records / $records_per_page) : 1;

    // Fetch search history records for the current page
    $sql = "SELECT search_id, Protein_Family, Taxonomy, search_time 
            FROM search_history 
            WHERE username = ? 
            ORDER BY search_time DESC 
            LIMIT ? OFFSET ?";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
    $stmt->bindParam(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $history_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error fetching search history: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
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
            padding: 8px 12px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
        }
        .pagination a.disabled {
            background-color: #ccc;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <h2>Search History for <?php echo htmlspecialchars($username); ?></h2>

    <?php if ($total_records > 0): ?>
        <table>
            <tr>
                <th>Search ID</th>
                <th>Protein Family</th>
                <th>Taxonomy</th>
                <th>Timestamp</th>
                <th>Results</th>
            </tr>
            <?php foreach ($history_records as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['search_id']); ?></td>
                    <td><?php echo htmlspecialchars($record['Protein_Family']); ?></td>
                    <td><?php echo htmlspecialchars($record['Taxonomy']); ?></td>
                    <td><?php echo htmlspecialchars($record['search_time']); ?></td> <!-- 修正字段名 -->
                    <td><a href="results.php?search_id=<?php echo urlencode($record['search_id']); ?>">View Results</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="history.php?page=<?php echo $page - 1; ?>">Previous</a>
            <?php else: ?>
                <a class="disabled">Previous</a>
            <?php endif; ?>

            Page <?php echo $page; ?> of <?php echo $total_pages; ?>

            <?php if ($page < $total_pages): ?>
                <a href="history.php?page=<?php echo $page + 1; ?>">Next</a>
            <?php else: ?>
                <a class="disabled">Next</a>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <p>No search history found.</p>
    <?php endif; ?>

</body>
</html>
