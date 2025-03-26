<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bioinformatics Tools Platform</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bioinformatics Tools Platform</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="https://bioinfmsc8.bio.ed.ac.uk/~s2746775/website/results.php?search_id=search_67e43bf6e7acb5.85963812&protein_family=glucose-6-phosphatase&taxonomy=Aves">Example Data</a></li>
                <li><a href="history.php">History</a></li>
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

    <section class="banner">
        <h2>Welcome to the Bioinformatics Tools Platform</h2>
        <p>Providing sequence alignment, structure prediction, and more.</p>
        <button id="learn-more">Learn More</button>
    </section>

    <section class="search">
        <h3>Search Protein Family and Taxonomy</h3>
        <form id="search-form">
            <label for="protein-family">Protein Family:</label>
            <input type="text" id="protein-family" name="protein-family" placeholder="Enter protein family" required>
            
            <label for="taxonomy">Taxonomy:</label>
            <input type="text" id="taxonomy" name="taxonomy" placeholder="Enter taxonomy" required>
            
            <div id="error-message"></div>

            <button type="submit">Search</button>
        </form>
    </section>

    <footer>
        <p>© 2025 Bioinformatics Tools Platform. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById("search-form").addEventListener("submit", function(event) {
            event.preventDefault(); 

            let formData = new FormData(this);
            let proteinFamily = document.getElementById("protein-family").value;
            let taxonomy = document.getElementById("taxonomy").value;

            fetch("search.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let messageBox = document.getElementById("error-message");
                messageBox.style.display = "block"; 
                if (data.status === "success") {
                    let url = "results.php?search_id=" + encodeURIComponent(data.search_id) +
                      "&protein_family=" + encodeURIComponent(proteinFamily) +
                      "&taxonomy=" + encodeURIComponent(taxonomy);

                    window.location.href = url;                    
                } else {
                    messageBox.innerText = "Search failed: " + data.message;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Request failed." + error.message);
            });
        });
    </script>
</body>
</html>
