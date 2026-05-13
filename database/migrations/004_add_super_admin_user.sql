ALTER TABLE users
    ADD username VARCHAR(50) UNIQUE AFTER user_id;


INSERT INTO users (
    role_id,
    first_name,
    last_name,
    mobile,
    username,
    password,
    status
)
VALUES (
           1,
           'Mehrab',
           'KB',
           '09369849997',
           'mehrabkb',
           '$2y$10$wH8K5QY9Yx4x8l9k2fYxN.jY9jz8mM0K2m4wL1v8M0M5x9YQ8Wz2K',
           'active'
       );
