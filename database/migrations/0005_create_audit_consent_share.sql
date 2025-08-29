CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_user_id BIGINT UNSIGNED NULL,
    actor_ip VARCHAR(45) NULL,
    action VARCHAR(100) NOT NULL,
    target_type ENUM('membership','trainee','payment','file','user') NOT NULL,
    target_id BIGINT UNSIGNED NULL,
    changes_json JSON NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_target (target_type, target_id),
    INDEX idx_actor (actor_user_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE consent_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    target_type ENUM('membership','trainee') NOT NULL,
    target_id BIGINT UNSIGNED NOT NULL,
    terms_version VARCHAR(20) NOT NULL,
    consent_text_snapshot TEXT NOT NULL,
    ip VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_target (target_type, target_id),
    INDEX idx_created_at (created_at)
);

CREATE TABLE share_links (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    token CHAR(36) NOT NULL UNIQUE,
    target_type ENUM('membership','trainee') NOT NULL,
    target_id BIGINT UNSIGNED NOT NULL,
    created_by_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_target (target_type, target_id),
    INDEX idx_token (token),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);