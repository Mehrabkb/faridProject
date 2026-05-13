CREATE TABLE roles (
                       role_id BIGINT AUTO_INCREMENT PRIMARY KEY,

                       name VARCHAR(50) NOT NULL UNIQUE,
                       slug VARCHAR(50) NOT NULL UNIQUE,

                       description TEXT NULL,

                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                           ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE users (
                       user_id BIGINT AUTO_INCREMENT PRIMARY KEY,

                       role_id BIGINT NOT NULL DEFAULT 3,

                       first_name VARCHAR(100) NOT NULL,
                       last_name VARCHAR(100) NOT NULL,

                       national_code CHAR(10) UNIQUE NULL,

                       mobile VARCHAR(20) NOT NULL UNIQUE,
                       email VARCHAR(150) UNIQUE NULL,

                       password VARCHAR(255) NOT NULL,

                       two_factor_code VARCHAR(10) NULL,
                       two_factor_expire DATETIME NULL,

                       mobile_verified_at DATETIME NULL,
                       email_verified_at DATETIME NULL,

                       avatar VARCHAR(255) NULL,

                       status ENUM(
        'active',
        'inactive',
        'banned',
        'pending'
    ) DEFAULT 'pending',

                       last_login_at DATETIME NULL,
                       last_login_ip VARCHAR(45) NULL,

                       remember_token VARCHAR(255) NULL,

                       failed_login_attempts INT DEFAULT 0,
                       locked_until DATETIME NULL,

                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                           ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX idx_users_role_id
    ON users(role_id);

CREATE INDEX idx_users_mobile
    ON users(mobile);

CREATE INDEX idx_users_email
    ON users(email);

CREATE INDEX idx_users_status
    ON users(status);

CREATE INDEX idx_users_national_code
    ON users(national_code);
INSERT INTO roles (
    name,
    slug,
    description
)
VALUES
    (
        'مدیر کل',
        'super_admin',
        'دسترسی کامل'
    ),
    (
        'مدیر',
        'admin',
        'مدیریت سیستم'
    ),
    (
        'کاربر',
        'user',
        'کاربر عادی'
    );
