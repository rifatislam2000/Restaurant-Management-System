CREATE DATABASE IF NOT EXISTS restaurant_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE restaurant_db;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'cashier',
    status TINYINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS foods (
    food_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    food_name VARCHAR(120) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    availability VARCHAR(20) NOT NULL DEFAULT 'Available',
    CONSTRAINT fk_food_category FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    customer_id INT NULL,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    order_status VARCHAR(20) NOT NULL DEFAULT 'Completed',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_customer FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_order_user FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_order FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_item_food FOREIGN KEY (food_id)
        REFERENCES foods(food_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Default administrator account.
-- Username: admin
-- Password: Admin@123
-- Password is stored as a PHP password_hash(), not plain text.
INSERT INTO users (full_name, username, password, role, status)
SELECT 'Administrator', 'admin', '$2y$12$/Tw7q9nOSY7jjii08bpkaOZJkszA/vWbSm1zjF7zb51aYIBAlRFs.', 'admin', 1
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin'
);

-- A few starter categories and food items for dashboard testing.
INSERT INTO categories (category_name, description)
SELECT 'Burger', 'Burger items' WHERE NOT EXISTS
(SELECT 1 FROM categories WHERE category_name = 'Burger');
INSERT INTO categories (category_name, description)
SELECT 'Pizza', 'Pizza items' WHERE NOT EXISTS
(SELECT 1 FROM categories WHERE category_name = 'Pizza');
INSERT INTO categories (category_name, description)
SELECT 'Drinks', 'Cold and soft drinks' WHERE NOT EXISTS
(SELECT 1 FROM categories WHERE category_name = 'Drinks');

INSERT INTO foods (category_id, food_name, price, description, availability)
SELECT category_id, 'Chicken Burger', 250.00, 'Starter sample food item', 'Available'
FROM categories WHERE category_name = 'Burger'
AND NOT EXISTS (SELECT 1 FROM foods WHERE food_name = 'Chicken Burger');
INSERT INTO foods (category_id, food_name, price, description, availability)
SELECT category_id, 'Regular Pizza', 450.00, 'Starter sample food item', 'Available'
FROM categories WHERE category_name = 'Pizza'
AND NOT EXISTS (SELECT 1 FROM foods WHERE food_name = 'Regular Pizza');
INSERT INTO foods (category_id, food_name, price, description, availability)
SELECT category_id, 'Soft Drink', 50.00, 'Starter sample food item', 'Available'
FROM categories WHERE category_name = 'Drinks'
AND NOT EXISTS (SELECT 1 FROM foods WHERE food_name = 'Soft Drink');
