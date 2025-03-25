import sys
import subprocess

def run_blast(sequence, output_file):
    """Run BLASTp with the given protein sequence and save the result."""
    db_path = "/localdisk/home/ubuntu-software/blast216/ReleaseMT/ncbidb/nr"
    blastp_path = "/localdisk/home/ubuntu-software/ncbi-blast-2.13.0+-src/bin/blastp"
    
    # Run BLASTP command
    command = f"echo '{sequence}' | {blastp_path} -db {db_path} -outfmt 6 -max_target_seqs 10 -num_threads 4"
    result = subprocess.run(command, shell=True, capture_output=True, text=True)

    if result.returncode != 0:
        error_message = f"Error: {result.stderr.strip()}"
        print(error_message, file=sys.stderr)  # Print error for PHP to capture
        sys.exit(1)  # Exit with error code

    # Save BLASTP output to file
    with open(output_file, "w") as f:
        f.write(result.stdout.strip())
    
    print("BLASTP analysis completed.")

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python3 blast_analysis.py <protein_id> <sequence> <output_file>", file=sys.stderr)
        sys.exit(1)
    
    protein_id = sys.argv[1]
    sequence = sys.argv[2]
    output_file = sys.argv[3]

    run_blast(sequence, output_file)
