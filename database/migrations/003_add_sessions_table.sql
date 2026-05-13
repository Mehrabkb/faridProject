CREATE TABLE sessions (
                          session_id BIGINT AUTO_INCREMENT PRIMARY KEY,

                          user_id BIGINT NOT NULL,

                          token VARCHAR(255) NOT NULL UNIQUE,

                          ip_address VARCHAR(45) NULL,

                          user_agent TEXT NULL,

                          last_activity DATETIME NOT NULL,

                          expires_at DATETIME NOT NULL,

                          is_valid TINYINT(1) DEFAULT 1,

                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sessions_user_id
    ON sessions(user_id);

CREATE INDEX idx_sessions_token
    ON sessions(token);

CREATE INDEX idx_sessions_expires_at
    ON sessions(expires_at);
