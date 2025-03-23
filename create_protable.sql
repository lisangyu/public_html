USE s2746775;
CREATE TABLE protein_sequences (
    search_id VARCHAR(255) NOT NULL,
    protein_id VARCHAR(255) NOT NULL,
    protein_name VARCHAR(255) NOT NULL,
    species VARCHAR(255) NOT NULL,
    sequence TEXT NOT NULL
);
