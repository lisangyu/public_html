index.html: Homepage. If the query success, go to the results.html.
search.php: Call shell script fetch_protein_data.sh to fetch protein data.
fetch_protein_data.sh: Shell script to fetch protein accession number search and download the FASTA files. Call store_protein_results.php.
store_protein_results.php: Directly store the accession number to database.
results.html: Display the protein accession nember for users to select for downstream analysis.
