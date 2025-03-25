USE s2746775;
CREATE TABLE protein_analysis (
    protein_id VARCHAR(255) PRIMARY KEY,
    blastp_result JSON NOT NULL,
    FOREIGN KEY (protein_id) REFERENCES protein_sequences(protein_id)
);