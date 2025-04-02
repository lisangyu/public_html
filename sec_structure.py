import os
import sys
import subprocess

def clean_sequence(sequence):
    """Remove invalid amino acids (X, B, Z, U, *)"""
    allowed = set("ACDEFGHIKLMNPQRSTVWY")
    return "".join([aa for aa in sequence if aa in allowed])

def run_garnier(protein_id, sequence):
    """Use EMBOSS garnier to predict secondary structure"""

    # Clean sequence
    sequence = clean_sequence(sequence)

    # Create the structure_results directory
    output_dir = "structure_results"
    os.makedirs(output_dir, exist_ok=True)

    # Save the input sequence to a temporary FASTA file
    fasta_file = f"{protein_id}.fasta"
    with open(fasta_file, "w") as f:
        f.write(f">{protein_id}\n{sequence}\n")

    output_file = os.path.join(output_dir, f"{protein_id}_structure.txt")

    # Run garnier to predict secondary structure and specify the output file
    result = subprocess.run(
        ["garnier", "-sequence", fasta_file, "-outfile", output_file],
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True
    )

    os.remove(fasta_file)

    if os.path.exists(output_file):
        print(f"Secondary structure analysis is complete, results saved in {output_file}")
    else:
        print("Secondary structure analysis failed, no output file was generated.")
        print("Error message:", result.stderr)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python script.py <protein_id> <sequence>")
        sys.exit(1)

    protein_id = sys.argv[1]
    sequence = sys.argv[2]

    run_garnier(protein_id, sequence)
