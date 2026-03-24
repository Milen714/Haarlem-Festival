CREATE TABLE IF NOT EXISTS LOGS (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    level VARCHAR(20) NOT NULL DEFAULT 'INFO',
    category VARCHAR(100) NOT NULL,
    message LONGTEXT NOT NULL,
    context JSON,
    stack_trace LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_level (level),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
