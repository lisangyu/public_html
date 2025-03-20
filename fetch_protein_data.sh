#!/bin/bash

# Check if exactly three arguments (Protein Family, Taxonomy, Search ID) are provided
if [ "$#" -ne 3 ]; then
    echo "Usage: $0 <protein_family> <taxonomy> <search_id>"
    exit 1
fi

# Assign input arguments
protein_family=$1
taxonomy=$2
search_id=$3

# Generate output file name
output_file="protein_results/${search_id}.fasta"
id_file="protein_results/${search_id}_ids.txt"

# Ensure results directory exists
mkdir -p protein_results

# Set NCBI email for Entrez Direct
email="lisangyu1005@outlook.com"

echo "Searching for protein family: $protein_family, taxonomy: $taxonomy..."

# Use EDirect to retrieve protein accession numbers instead of UIDs
protein_accessions=$(esearch -db protein -query "$protein_family [Protein Name] AND $taxonomy [Organism]" | efetch -format acc)

# Check if any protein accessions were found
if [ -z "$protein_accessions" ]; then
    echo "No proteins found for family: $protein_family in taxonomy: $taxonomy."
    exit 1
fi

# Save the found protein accessions to a text file
#echo "$protein_accessions" > "id_file"

# Fetch sequences for all found accessions and save to output file
echo "Fetching protein sequences..."
for accession in $protein_accessions; do
    efetch -db protein -id "$accession" -format fasta >> "$output_file"
done

# Verify if the sequences were successfully retrieved
if [ -s "$output_file" ]; then
    echo "Protein sequences fetched successfully. The sequences are saved to $output_file."
else
    echo "Failed to fetch protein sequences."
    exit 1
fi

# Store protein IDs in the database
#php store_protein_results.php "$search_id" "$id_file"

# Store protein IDs into a PHP script for direct database insertion
# Use cURL to call the PHP script and pass the protein accessions to it
php_store_url="http://localhost/store_protein_results.php"
curl --data "search_id=$search_id&protein_ids=$protein_accessions" "$php_store_url"
