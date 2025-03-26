import os
import sys
import subprocess

def run_garnier(protein_id, sequence):
    """Use EMBOSS garnier to predict secondary structure"""
    
    # Create the structure_results directory
    output_dir = "structure_results"
    os.makedirs(output_dir, exist_ok=True)

    # Save the input sequence to a temporary FASTA file
    fasta_file = f"{protein_id}.fasta"
    with open(fasta_file, "w") as f:
        f.write(f"> {protein_id}\n{sequence}\n")

    # Set the output file path
    output_file = os.path.join(output_dir, f"{protein_id}_structure.txt")
    
    # Run garnier to predict secondary structure and specify the output file
    result = subprocess.run(
        ["garnier", "-sequence", fasta_file, "-outfile", output_file],  # Use -outfile to specify the output file
        stdout=subprocess.PIPE,  # Capture standard output
        stderr=subprocess.PIPE,  # Capture standard error
        text=True  # Get the output in text format
    )

    # Remove the temporary FASTA file
    os.remove(fasta_file)

    # Notify about the analysis completion
    if os.path.exists(output_file):
        print(f"Secondary structure analysis is complete, results saved in {output_file}")
    '''else:
        print("Secondary structure analysis failed, no output file was generated.")'''

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python script.py <protein_id> <sequence>")
        sys.exit(1)

    protein_id = sys.argv[1]
    sequence = sys.argv[2]

    run_garnier(protein_id, sequence)
