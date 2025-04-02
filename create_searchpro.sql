USE s2746775;
CREATE TABLE search_protein (
    search_id VARCHAR(255) NOT NULL,
    protein_id VARCHAR(255) NOT NULL
    FOREIGN KEY (search_id) REFERENCES search_history(search_id) ON DELETE CASCADE
);
