CREATE TABLE membership_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status ENUM('draft','submitted','payment_received','paid','confirmed') NOT NULL DEFAULT 'draft',
    full_name VARCHAR(150) NULL,
    email VARCHAR(150) NULL,
    gender VARCHAR(30) NULL,
    dob DATE NULL,
    draft_data JSON NULL,
    submitted_at DATETIME NULL,
    paid_at DATETIME NULL,
    confirmed_at DATETIME NULL,
    created_ip VARCHAR(45) NULL,
    submitted_ip VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_email (email)
);

CREATE TABLE trainee_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status ENUM('draft','submitted','payment_received','paid','confirmed') NOT NULL DEFAULT 'draft',
    training_for ENUM('self','other') NULL,
    trainee_type ENUM('junior','senior') NULL,
    bgf_id VARCHAR(50) NULL,
    draft_data JSON NULL,
    submitted_at DATETIME NULL,
    paid_at DATETIME NULL,
    confirmed_at DATETIME NULL,
    created_ip VARCHAR(45) NULL,
    submitted_ip VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_bgf_id (bgf_id)
);