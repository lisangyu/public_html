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
#id_file="protein_results/${search_id}_ids.txt"

# Ensure results directory exists
mkdir -p protein_results

# Set NCBI email for Entrez Direct
email="lisangyu1005@outlook.com"

# Use EDirect to retrieve protein accession numbers instead of UIDs
protein_accessions=$(/home/s2746775/edirect/esearch -db protein -query "$protein_family [Protein Name] AND $taxonomy [Organism]" | /home/s2746775/edirect/efetch -format acc)

# Check if any protein accessions were found
if [ -z "$protein_accessions" ]; then
    echo "No proteins found for family: $protein_family in taxonomy: $taxonomy."
    exit 1
fi

# Save the found protein accessions to a text file
#echo "$protein_accessions" > "$id_file"

# Fetch sequences for all found accessions and save to output file
for accession in $protein_accessions; do
    # Fetch the FASTA sequence for each accession
    sequence=$(/home/s2746775/edirect/efetch -db protein -id "$accession" -format fasta)
    
    # Append sequence to the output file
    echo "$sequence" >> "$output_file"
done

#echo "$protein_accessions"

# Verify if the sequences were successfully retrieved
if [ -s "$output_file" ]; then
    # Only return the protein accessions (not the other log messages)
    #echo "successful: $protein_accessions"
    echo "$protein_accessions"
else
    echo "Failed to fetch protein sequences."
    exit 1
fi
