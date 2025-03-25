USE s2746775;
CREATE TABLE conservation_results (
    search_id VARCHAR(255) PRIMARY KEY,
    alignment_content LONGTEXT NOT NULL,
    plot_image TEXT NOT NULL
);
