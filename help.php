<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Page</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="header.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
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
        </div>
    </header>

    <main>
        <div class="container1">
            <h2>What This Website Offers</h2>
            
            <section>
                <h3>1. Home Page</h3>
                <p>
                    The Home page offers a navigation menu with the following options: 
                    <strong>Example Data</strong>, <strong>History</strong>, <strong>Credits</strong>, 
                    <strong>Register</strong>, and <strong>Login</strong>.
                </p>
                <p>
                    Below the menu, there is a search bar where users can input the <strong>Protein Family</strong> 
                    and <strong>Taxonomy</strong> to query the <a href="https://www.ncbi.nlm.nih.gov" target="_blank">NCBI database</a>.
                    If the query is successful, the page will redirect to the results page. Each query has a unique <strong>Search ID</strong>.
                </p>
                <p>
                    Users can query without logging in, but only logged-in users can save their search history. 
                    To view the history, click on the <strong>History</strong> link in the menu. The system will remember previous queries 
                    and retrieve the results instantly, reducing wait time.
                </p>
                <p>
                    If a query fails, users will see an error message informing them that the protein does not exist or the query is invalid.
                </p>
            </section>

            <section>
                <h3>2. Results Page</h3>
                <p>
                    On the <strong>Results Page</strong>, users can view details of proteins of interest, including:
                    <ul>
                        <li><strong>Accession number</strong></li>
                        <li><strong>Protein name</strong></li>
                        <li><strong>Species name</strong></li>
                        <li><strong>Protein sequence</strong></li>
                    </ul>
                    The results are displayed in a table, with 20 entries per page. Users can sort the entries by species name to 
                    easily find proteins from their desired species.
                </p>
                <p>
                    By clicking on a protein's <strong>Accession Number</strong>, users can perform a detailed analysis of the protein.
                    Additionally, users can select proteins via checkboxes for multiple sequence alignment using <strong>Clustalo</strong>.
                </p>
                <p>
                    If there are multiple results, users can click the "Perform Conservation Level Analysis on All Proteins" button to access a visualization of the protein conservation.
                </p>
            </section>

            <section>
                <h3>3. Protein Analysis Page</h3>
                <p>
                    After clicking on a protein's <strong>Accession Number</strong>, users will be taken to a page with multiple analyses:
                    <ul>
                        <li><strong>Motifs analysis</strong></li>
                        <li><strong>Protein properties</strong></li>
                        <li><strong>Secondary structure</strong></li>
                    </ul>
                    Users can download the analysis results at the bottom of the page.
                </p>
            </section>

            <section>
                <h3>4. Multiple Sequence Alignment</h3>
                <p>
                    Users can select two or more proteins on the results page, and click the <strong>"Run MSA (Clustal Omega)"</strong> button 
                    to view the alignment results, generated using <strong>Clustalo</strong>.
                </p>
            </section>

            <section>
                <h3>5. Conservation Analysis</h3>
                <p>
                    The website provides protein conservation analysis, where users can visualize conservation results from <strong>Clustalo</strong> multiple sequence alignments using <strong>Plotcon</strong>.
                    Users can download the conservation analysis results at the bottom of the page.
                </p>
            </section>

            <section>
                <h3>6. Registration and Login</h3>
                <p>
                    During registration, users must provide a unique <strong>username</strong>, a <strong>password</strong>, and confirm the password. 
                    After registering, users will be automatically logged in. For subsequent logins, users must enter their <strong>username</strong> 
                    and <strong>password</strong>.
                </p>
                <p>
                    Upon logging in, users can view their login status in the menu. To log out, simply click the <strong>Logout</strong> link in the menu.
                </p>
            </section>

            <section>
                <h3>7. History Page</h3>
                <p>
                    After logging in, users can access their search history by clicking the <strong>History</strong> link in the menu.
                    The history page presents a table of all past queries, including:
                    <ul>
                        <li><strong>Search ID</strong></li>
                        <li><strong>Protein Family</strong></li>
                        <li><strong>Taxonomy</strong></li>
                        <li><strong>Search Time</strong></li>
                        <li><strong>Results</strong> (with a link to the result page)</li>
                    </ul>
                    By clicking the link in the <strong>Results</strong> column, users will be redirected to the respective results page for further analysis, without waiting for a new search to complete.
                </p>
            </section>
        </div>
    </main>

</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .container1 {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
    }

    h1, h2, h3 {
        color: #333;
        font-weight: bold;
    }

    p {
        font-size: 1rem;

        margin-bottom: 1.5rem;
    }

    ul {
        margin: 1rem 0;
        padding-left: 20px;
    }

    li {
        font-size: 1rem;

    }

    main {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    section {
        margin-bottom: 2rem;
    }

    section h3 {
        color: #007bff;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    section p {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
    }

</style>