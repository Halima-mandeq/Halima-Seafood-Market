-- Halima Seafood Market - Authentication System Schema
-- database.sql

USE halima_seafood_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category ENUM('Fresh', 'Frozen', 'Shellfish', 'Canned') DEFAULT 'Fresh',
    description TEXT,
    price_per_kg DECIMAL(10, 2) NOT NULL,
    stock_level_kg DECIMAL(10, 2) DEFAULT 0,
    status ENUM('In Stock', 'Low Stock', 'Out of Stock') DEFAULT 'In Stock',
    image_path VARCHAR(255) DEFAULT 'default_fish.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    weight_kg DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Special Prices Table (for personalized pricing offers)
CREATE TABLE IF NOT EXISTS special_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    special_price DECIMAL(10, 2) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert main admin if not exists (default password: 'password123')
INSERT IGNORE INTO users (full_name, email, phone_number, username, password, role, status) 
VALUES 
('Admin User', 'admin@halimaseafood.com', '1234567890', 'Halimo10', '$2y$12$oXtxcf3z7q8De63528UL3dBWKi4cEsuuN.pMvU.K8F.v/HksdGC', 'admin', 'active'),
('Admin Tester', 'tester@halimaseafood.com', '1234567891', 'admin123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
('Amina Yusuf', 'amina@example.com', '0987654321', 'amina', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
('Sarah Chen', 'sarah@example.com', '0987654322', 'sarah', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
('John Doe', 'john@example.com', '0987654323', 'johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
('David Miller', 'david@example.com', '0987654324', 'davidm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active');

-- Insert base products
INSERT IGNORE INTO products (sku, name, category, description, price_per_kg, stock_level_kg, status, image_path)
VALUES 
('SALM-001', 'Atlantic Salmon', 'Fresh', 'Premium fresh Atlantic salmon fillets.', 24.99, 120.00, 'In Stock', 'salmon.png'),
('TUNA-442', 'Yellowfin Tuna', 'Fresh', 'Wild-caught yellowfin tuna steaks.', 35.00, 15.00, 'Low Stock', 'tuna.png'),
('PRWN-009', 'Tiger Prawns', 'Frozen', 'Large frozen tiger prawns, head-on.', 18.50, 0.00, 'Out of Stock', 'prawns.png'),
('MKRL-882', 'King Mackerel', 'Fresh', 'Freshly caught king mackerel.', 22.00, 85.00, 'In Stock', 'mackerel.png');

-- Insert sample orders
INSERT IGNORE INTO orders (user_id, product_id, weight_kg, total_price, status, created_at)
VALUES 
(3, 1, 5.5, 120.00, 'Processing', '2023-10-12 10:00:00'),
(4, 2, 3.0, 85.50, 'Delivered', '2023-10-12 11:30:00'),
(3, 3, 10.2, 210.00, 'Pending', '2023-10-11 14:15:00'),
(4, 4, 4.0, 92.00, 'Delivered', '2023-10-10 09:45:00');

-- Insert sample messages
-- Admin (1) chatting with Amina (3)
INSERT IGNORE INTO messages (sender_id, receiver_id, message, is_read, created_at)
VALUES 
(3, 1, 'Hi, I wanted to check if you have any jumbo tiger shrimp available for delivery this afternoon? My previous order #8829 was excellent!', 1, '2023-10-14 10:15:00'),
(1, 3, 'Hello Amina! We\'re glad you enjoyed the previous order. Let me check the fresh inventory for you right now.', 1, '2023-10-14 10:18:00'),
(3, 1, 'Is the jumbo shrimp back in stock? I need them for a dinner party tonight!', 0, '2023-10-14 10:20:00');

-- Other customers sent messages
INSERT IGNORE INTO messages (sender_id, receiver_id, message, is_read, created_at)
VALUES 
(4, 1, 'I need to change my delivery address for my current order.', 0, '2023-10-14 09:30:00'),
(5, 1, 'Thank you for the quick delivery.', 1, '2023-10-14 07:15:00'),
(6, 1, 'Can I get a refund for the crushed...', 1, '2023-10-13 16:45:00');
