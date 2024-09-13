CREATE TABLE audit_log
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    action     VARCHAR(255) NOT NULL,
    user_id    INT          NOT NULL,
    entity     VARCHAR(255) NOT NULL,
    entity_id  INT          NOT NULL,
    old_value  TEXT,
    new_value  TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
