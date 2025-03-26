USE s2746775;
CREATE TABLE protein_analysis (
    protein_id VARCHAR(255) PRIMARY KEY,
    sequence TEXT NOT NULL,
    motif_results LONGTEXT NOT NULL,
    property_results LONGTEXT NOT NULL,
    structure_results LONGTEXT NOT NULL,
    FOREIGN KEY (protein_id) REFERENCES protein_sequences(protein_id) ON DELETE CASCADE
);
