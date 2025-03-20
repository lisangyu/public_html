#!/bin/bash

# Check if exactly two arguments (Protein Family and Taxonomy) are provided
if [ "$#" -ne 3 ]; then
    echo "Usage: $0 <protein_family> <taxonomy> <search_id>"
    exit 1
fi

# Assign input arguments
protein_family=$1
taxonomy=$2
search_id=$3

# Generate the output file name
output_file="${search_id}.fasta"

# Set NCBI email
email="lisangyu1005@outlook.com"

echo "Searching for proteins in family: $protein_family, taxonomy: $taxonomy..."

# Use EDirect to find relevant protein IDs
protein_ids=$(esearch -db protein -query "$protein_family [Protein Name] AND $taxonomy [Organism]" | efetch -format uid)

# Check if any protein IDs were found
if [ -z "$protein_ids" ]; then
    echo "No proteins found for family: $protein_family in taxonomy: $taxonomy."
    exit 1
fi

echo "Found protein IDs: $protein_ids"

# Fetch sequences for all found proteins
echo "Fetching protein sequences..."
for protein_id in $protein_ids; do
    efetch -db protein -id "$protein_id" -format fasta >> "$output_file"
done

# Check if sequences were retrieved
if [ -s "$output_file" ]; then
    echo "Protein sequences fetched successfully. The sequences are saved to $output_file."
else
    echo "Failed to fetch protein sequences."
    exit 1
fi
