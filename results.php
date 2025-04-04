<?php
include 'config.php';
session_start();

$limit = 20;

// Get search_id from GET parameter
$search_id = $_GET['search_id'] ?? '';
$protein_family = isset($_GET['protein_family']) ? htmlspecialchars($_GET['protein_family']) : 'Not provided';
$taxonomy = isset($_GET['taxonomy']) ? htmlspecialchars($_GET['taxonomy']) : 'Not provided';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Check if species sort is requested
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default order is ASC
$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'species'; // Default sort column is species

if (empty($search_id)) {
    die("Error: search_id is required.");
}

// Get total count of results for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) 
                       FROM search_protein sp 
                       JOIN protein_sequences ps ON sp.protein_id = ps.protein_id 
                       WHERE sp.search_id = ?");
$stmt->execute([$search_id]);
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch protein sequences for the given search_id with pagination and sorting
$sql = "SELECT ps.protein_id, ps.protein_name, ps.species, ps.sequence 
        FROM search_protein sp
        JOIN protein_sequences ps ON sp.protein_id = ps.protein_id
        WHERE sp.search_id = ?
        ORDER BY $orderBy $order 
        LIMIT $limit OFFSET $offset";
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
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="results_style.css">
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
        button[disabled] {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .warning {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="https://bioinfmsc8.bio.ed.ac.uk/~s2746775/website/results.php?search_id=search_67e43bf6e7acb5.85963812&protein_family=glucose-6-phosphatase&taxonomy=Aves">Example Data</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="help.php">Help/Context</a></li>
                <li><a href="credits.php">Credits</a></li>

                <?php if (isset($_SESSION['username'])): ?>
                    <li>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <h2>Search Results for Protein Family: <?= htmlspecialchars($protein_family) ?>, Taxonomy: <?= htmlspecialchars($taxonomy) ?></h2>

    <p>Total Results: <?= $total_rows ?> protein sequences found.</p>

    <?php if (count($results) > 0): ?>
        <form id="analysis-form" action="msa_results.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all" /> Select All (for MSA)
                        </th>
                        <th>Protein ID</th>
                        <th>Protein Name</th>
                        <th>
                            <a href="?search_id=<?= urlencode($search_id) ?>&protein_family=<?= urlencode($protein_family) ?>&taxonomy=<?= urlencode($taxonomy) ?>&orderby=species&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">Species</a>
                        </th>
                        <th>Sequence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><input type="checkbox" class="protein-checkbox" name="selected_proteins[]" value="<?= htmlspecialchars($row['protein_id']) ?>"></td>
                            <td><a href="protein_analysis.php?protein_id=<?= urlencode($row['protein_id']) ?>" target="_blank"><?= htmlspecialchars($row['protein_id']) ?></a></td>
                            <td><?= htmlspecialchars($row['protein_name']) ?></td>
                            <td><?= htmlspecialchars($row['species']) ?></td>
                            <td style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($row['sequence'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?search_id=<?= urlencode($search_id) ?>&protein_family=<?= urlencode($protein_family) ?>&taxonomy=<?= urlencode($taxonomy) ?>&page=<?= $page - 1 ?>&orderby=<?= urlencode($orderBy) ?>&order=<?= urlencode($order) ?>">« Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?search_id=<?= urlencode($search_id) ?>&protein_family=<?= urlencode($protein_family) ?>&taxonomy=<?= urlencode($taxonomy) ?>&page=<?= $i ?>&orderby=<?= urlencode($orderBy) ?>&order=<?= urlencode($order) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?search_id=<?= urlencode($search_id) ?>&protein_family=<?= urlencode($protein_family) ?>&taxonomy=<?= urlencode($taxonomy) ?>&page=<?= $page + 1 ?>&orderby=<?= urlencode($orderBy) ?>&order=<?= urlencode($order) ?>">Next »</a>
                <?php endif; ?>
            </div>
            <div class="button-container">
                <button type="submit" id="run-msa-btn" disabled>Run MSA (Clustal Omega)</button>
                <a href="conservation_analysis.php?search_id=<?= urlencode($search_id) ?>" id="conservation-analysis-link"
                    <?php if ($total_rows <= 1): ?> 
                        style="pointer-events: none; color: white; background-color: #ccc; cursor: not-allowed;" 
                    <?php endif; ?>>
                    Perform Conservation Level Analysis on All Proteins
                </a>
            </div>

            <p id="warning" class="warning" style="display:none;">Please select at least two proteins for MSA!</p>

        </form>


    <?php else: ?>
        <p>No results found for this search.</p>
    <?php endif; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkboxes = document.querySelectorAll(".protein-checkbox");
            const selectAllCheckbox = document.getElementById("select-all");
            const msaButton = document.getElementById("run-msa-btn");
            const warningMessage = document.getElementById("warning");

            // Update button state based on checkbox selection
            function updateMSAButton() {
                const checkedCount = document.querySelectorAll(".protein-checkbox:checked").length;
                msaButton.disabled = checkedCount < 2; // Disable button if fewer than 2 proteins are selected
                warningMessage.style.display = checkedCount < 2 ? "block" : "none"; // Show warning if fewer than 2 proteins are selected
            }

            // Handle select all/deselect all
            selectAllCheckbox.addEventListener("change", function () {
                const isChecked = selectAllCheckbox.checked;
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateMSAButton();
            });

            // Handle individual checkbox changes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateMSAButton);
            });

            // Ensure user selects at least 2 proteins before submitting
            document.getElementById("analysis-form").addEventListener("submit", function (event) {
                const checkedCount = document.querySelectorAll(".protein-checkbox:checked").length;
                if (checkedCount < 2) {
                    event.preventDefault();
                    alert("Please select at least two proteins for MSA!");
                }
            });
        });
    </script>
</body>
</html>
