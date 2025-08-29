CREATE TABLE uploads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_type ENUM('membership','trainee') NOT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    path_raw VARCHAR(255) NOT NULL,
    path_optimized VARCHAR(255) NULL,
    uploaded_by_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_owner (owner_type, owner_id),
    INDEX idx_category (category),
    FOREIGN KEY (uploaded_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_type ENUM('membership','trainee') NOT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    payment_date DATE NOT NULL,
    mode ENUM('cheque','bank_transfer') NOT NULL,
    trx_id VARCHAR(100) NULL,
    proof_upload_id BIGINT UNSIGNED NULL,
    created_by_user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_owner (owner_type, owner_id),
    INDEX idx_payment_date (payment_date),
    FOREIGN KEY (proof_upload_id) REFERENCES uploads(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);