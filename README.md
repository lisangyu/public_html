index.html: Homepage. If the query success, go to the results.html.
search.php: Call shell script fetch_protein_data.sh to fetch protein data and store protein information to database.
fetch_protein_data.sh: Shell script to fetch protein accession number search and download the FASTA files. Return accession number to search.php.
results.html: Display the protein accession nember for users to select for downstream analysis.
