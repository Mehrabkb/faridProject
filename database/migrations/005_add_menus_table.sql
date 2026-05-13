CREATE TABLE menus (

                       menu_id INT AUTO_INCREMENT PRIMARY KEY,

                       parent_id INT DEFAULT NULL,

                       title VARCHAR(100) NOT NULL,

                       slug VARCHAR(100) DEFAULT NULL,

                       url VARCHAR(255) DEFAULT NULL,

                       icon VARCHAR(100) DEFAULT NULL,

                       sort_order INT DEFAULT 0,

                       is_active TINYINT(1) DEFAULT 1,

                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);
CREATE TABLE menu_permissions (

                                  id INT AUTO_INCREMENT PRIMARY KEY,

                                  menu_id INT NOT NULL,

                                  role_id INT NOT NULL

);
INSERT INTO menus
(title, parent_id, url, icon, sort_order)
VALUES

    ('داشبورد', NULL, 'dashboard.php', 'fas fa-home', 1),

    ('کاربران', NULL, '#', 'fas fa-users', 2),

    ('لیست کاربران', 2, 'users/index.php', 'far fa-circle', 1),

    ('افزودن کاربر', 2, 'users/create.php', 'far fa-circle', 2);
