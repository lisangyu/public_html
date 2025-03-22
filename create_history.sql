USE s2746775;
CREATE TABLE search_history (
    search_id VARCHAR(255) NOT NULL,
    username VARCHAR(50),
    Protein_Family VARCHAR(255) NOT NULL,
    Taxonomy VARCHAR(255) NOT NULL,
    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username)
);