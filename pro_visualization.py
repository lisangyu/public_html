import sys
import matplotlib.pyplot as plt

def visualize_sequence(sequence):
    """Generate a simple visualization of the protein sequence length."""
    fig, ax = plt.subplots()
    ax.barh(["Protein"], [len(sequence)], color='skyblue')
    ax.set_xlabel("Length")
    ax.set_title("Protein Sequence Length")
    plt.savefig("visualization.png")
    plt.close()

if __name__ == "__main__":
    protein_sequence = sys.argv[1]
    visualize_sequence(protein_sequence)
    #print("Visualization saved as visualization.png")
