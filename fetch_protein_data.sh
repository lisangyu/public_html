#!/bin/bash

# Check if exactly three arguments (Protein Family, Taxonomy, Search ID) are provided
if [ "$#" -ne 3 ]; then
    echo "Usage: $0 <protein_family> <taxonomy> <search_id>"
    exit 1
fi

protein_family=$1
taxonomy=$2
search_id=$3

# Generate output file name
output_file="protein_results/${search_id}.fasta"
mkdir -p protein_results
email="lisangyu1005@outlook.com"

# Use EDirect to retrieve protein accession numbers
protein_accessions=$(/home/s2746775/edirect/esearch -db protein -query "$protein_family [Protein Name] AND $taxonomy [Organism]" | /home/s2746775/edirect/efetch -format acc)

# Check if any protein accessions were found
if [ -z "$protein_accessions" ]; then
    echo "No proteins found for family: $protein_family in taxonomy: $taxonomy."
    exit 1
fi

# Save the found protein accessions to a text file
for accession in $protein_accessions; do
    sequence=$(/home/s2746775/edirect/efetch -db protein -id "$accession" -format fasta)
    echo "$sequence" >> "$output_file"
done

# Verify if the sequences were successfully retrieved
if [ -s "$output_file" ]; then
    echo "$protein_accessions"
else
    echo "Failed to fetch protein sequences."
    exit 1
fi
