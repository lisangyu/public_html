<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credits</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="header.css">
</head>
<body>
    <header>
        <h1>Statement of Credits</h1>
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

    <section class="credits">
        <h2>Code and Tool Credits</h2>
        <p>This platform uses various open-source libraries and AI tools. Special thanks to:</p>
        <ul>
            <li>OpenAI for providing the AI tools to assist in enhancing the website's user interface. It provided suggestions for improving the visual design, layout, and overall aesthetics of the web pages. Additionally, ChatGPT helped in the creation of pagination tables, offering guidance on how to structure and implement these tables efficiently. It also assisted in various issues related to table functionality, such as optimizing data display and addressing errors related to pagination and sorting.</li>
            <li>Protein data from NCBI</li>
            <li>Various bioinformatics libraries used for analysis</li>
            <li>Github link: <a href="https://github.com/lisangyu/public_html">https://github.com/lisangyu/public_html</a></li>
        </ul>
    </section>

</body>
</html>
