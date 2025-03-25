import sys
import subprocess
import os
import matplotlib.pyplot as plt
from Bio import AlignIO
import weblogo

# Function to run Clustal Omega for multiple sequence alignment
def run_clustal_omega(fasta_file, search_id):
    output_file = "conservation_results/" + search_id + ".aln"
    command = f"/usr/bin/clustalo -i {fasta_file} -o {output_file} --force --outfmt=clustal"
    subprocess.run(command, shell=True, check=True)
    return output_file

# Function to run PlotCon for conservation plot generation
def run_plotcon(alignment_file):
    command = f"/usr/bin/plotcon -sequence {alignment_file} -graph png -winsize 4"
    
    try:
        subprocess.run(command, shell=True, check=True)
    except subprocess.CalledProcessError as e:
        print("PlotCon failed:", e.stderr)
        return None
    default_output = "plotcon.1.png"
    output_file = f"conservation_results/{search_id}_plotcon.png"

    if os.path.exists(default_output):
        os.rename(default_output, output_file)
        return output_file
    else:
        print("Error: plotcon did not generate expected output.")
        return None
'''
# Function to run WebLogo using Python library
def run_weblogo(alignment_file):
    # Read aligned sequences from the .aln file
    alignment = AlignIO.read(alignment_file, "clustal")
    sequences = [str(record.seq) for record in alignment]

    # Create a frequency matrix
    seq_length = len(sequences[0])
    data = []
    for i in range(seq_length):
        column = [seq[i] for seq in sequences]
        counts = {base: column.count(base) for base in "ACGT"}
        total = sum(counts.values())
        if total == 0:
            frequencies = {base: 0 for base in "ACGT"}
        else:
            frequencies = {base: count / total for base, count in counts.items()}
        data.append(frequencies)

    # Create the WebLogo data format
    logo_data = []
    for row in data:
        logo_data.append([row.get('A', 0), row.get('C', 0), row.get('G', 0), row.get('T', 0)])

    # Create a WebLogo plot
    logo = weblogo.seqlogo(logo_data)
    output_file = alignment_file.replace(".aln", ".logo.png")
    logo.savefig(output_file)
    return output_file
'''
# Main function to handle conservation analysis
def conservation_analysis(fasta_file, search_id):
    # Step 1: Run Clustal Omega to get alignment
    alignment_file = run_clustal_omega(fasta_file, search_id)

    # Step 2: Run PlotCon to generate conservation plot
    cons_file = run_plotcon(alignment_file)

    # Step 3: Generate WebLogo using Python library
    #logo_file = run_weblogo(alignment_file)

    # Step 4: Plot and save the conservation plot (optional)
    plot_output = f"conservation_results/{search_id}_plotcon.png"
    '''plt.figure()
    with open(cons_file, 'r') as f:
        lines = f.readlines()
        scores = [float(line.strip()) for line in lines]
        plt.plot(scores)
        plt.title("Conservation Plot")
        plt.xlabel("Position")
        plt.ylabel("Conservation Score")
        plt.savefig(plot_output)'''

    # Return the path to the final plot
    return plot_output

if __name__ == "__main__":
    # Get the fasta file from the argument passed from PHP
    fasta_file = sys.argv[1]
    search_id = sys.argv[2]

    print(f"Python script started with arguments: fasta_file={fasta_file}, search_id={search_id}")
    if not os.path.exists(fasta_file):
        print(f"Error: FASTA file {fasta_file} does not exist.", file=sys.stderr)
        sys.exit(1)

    print(f"FASTA file {fasta_file} exists, proceeding with analysis...")

    # Run conservation analysis
    output_image = conservation_analysis(fasta_file, search_id)
    
    # Return the path of the output plot
    print(output_image)

