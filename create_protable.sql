USE s2746775;
CREATE TABLE protein_sequences (
    protein_id VARCHAR(255) PRIMARY KEY,
    protein_name VARCHAR(255) NOT NULL,
    species VARCHAR(255) NOT NULL,
    sequence TEXT NOT NULL
);
