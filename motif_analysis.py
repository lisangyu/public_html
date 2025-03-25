import subprocess
import os
import shutil
import argparse

def run_patmatmotifs(protein_id, sequence):
    """
    Run patmatmotifs to analyze motifs in a protein sequence.
    
    :param protein_id: Identifier for the protein.
    :param sequence: Protein sequence in FASTA format as a string.
    """
    # Ensure EMBOSS is installed
    if not shutil.which("patmatmotifs"):
        raise EnvironmentError("patmatmotifs is not installed or not in PATH.")
    
    # Define output file name
    output_file = f"/motif_results/{protein_id}_motif.txt"
    
    # Save input sequence to a temporary file
    input_file = f"{protein_id}.fasta"
    with open(input_file, "w") as f:
        f.write(sequence)
    
    # Run patmatmotifs
    cmd = ["patmatmotifs", "-sequence", input_file, "-outfile", output_file]
    try:
        subprocess.run(cmd, check=True)
        print(f"Analysis completed. Results saved in {output_file}")
    except subprocess.CalledProcessError as e:
        print("Error running patmatmotifs:", e)
    finally:
        # Cleanup temporary file
        if os.path.exists(input_file):
            os.remove(input_file)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Run patmatmotifs on a given protein sequence.")
    parser.add_argument("protein_id", type=str, help="Protein identifier")
    parser.add_argument("sequence", type=str, help="Protein sequence in FASTA format")
    args = parser.parse_args()
    
    run_patmatmotifs(args.protein_id, args.sequence)
