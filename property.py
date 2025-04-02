import os
import sys
import subprocess

def run_pepstats(protein_id, sequence):
    """Use EMBOSS pepstats for protein physicochemical property analysis"""
    
    # Create directory for storing property results
    output_dir = "property_results"
    os.makedirs(output_dir, exist_ok=True)

    fasta_file = f"{protein_id}.fasta"
    with open(fasta_file, "w") as f:
        f.write(f">{protein_id}\n{sequence}\n")

    # Run pepstats for analysis
    output_file = os.path.join(output_dir, f"{protein_id}_property.txt")
    with open(output_file, "w") as f:
        subprocess.run(["pepstats", "-sequence", fasta_file, "-outfile", output_file], check=True)

    os.remove(fasta_file)

    #print(f"Physicochemical property analysis is complete, results saved in {output_file}")

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python script.py <protein_id> <sequence>")
        sys.exit(1)

    protein_id = sys.argv[1]
    sequence = sys.argv[2]

    run_pepstats(protein_id, sequence)
