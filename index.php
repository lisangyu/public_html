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
    </section>

    <section class="search" style="text-align: center; margin: 0 auto; padding: 40px; background-color: rgba(255, 255, 255, 0.9); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;">
        <h3>Search Protein Family and Taxonomy</h3>
        <form id="search-form">
            <label for="protein-family">Protein Family:</label>
            <input type="text" id="protein-family" name="protein-family" placeholder="Enter protein family" required>
            
            <label for="taxonomy">Taxonomy:</label>
            <input type="text" id="taxonomy" name="taxonomy" placeholder="Enter taxonomy" required>
            
            <div id="error-message"></div>
            <div id="loading-message" style="display:none;">Searching, please wait...</div>

            <button type="submit" id="search-button">Search</button>
        </form>
    </section>

    <script>
        document.getElementById("search-form").addEventListener("submit", function(event) {
            event.preventDefault(); 

            let searchButton = document.getElementById("search-button");
            let loadingMessage = document.getElementById("loading-message");
            searchButton.disabled = true;
            loadingMessage.style.display = "block";
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
                loadingMessage.style.display = "none";
                searchButton.disabled = false;
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
                loadingMessage.style.display = "none";
                searchButton.disabled = false;
                alert("Request failed." + error.message);
            });
        });
    </script>
</body>
</html>
