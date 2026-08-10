-- FreshMart Grocery Store Database
-- Created for phpMyAdmin
-- Import this file directly into phpMyAdmin

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products ( 
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    category_id INT,
    image_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_date TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart table (for temporary cart storage)
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'manager') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create contact form submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    topic VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
);

-- Add index for better performance
CREATE INDEX idx_contact_email ON contact_submissions(email);
CREATE INDEX idx_contact_status ON contact_submissions(status);
CREATE INDEX idx_contact_created ON contact_submissions(created_at);

-- Insert default categories
INSERT INTO categories (name, description, image_url) VALUES
('Fruits', 'Fresh and seasonal fruits', 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?q=80&w=600&auto=format&fit=crop'),
('Vegetables', 'Fresh and seasonal vegetables', 'https://images.unsplash.com/photo-1597362925123-77861d3fbac7?q=80&w=600&auto=format&fit=crop'),
('Dairy', 'Fresh and seasonal dairy products', 'https://images.unsplash.com/photo-1528750901443-e986c782c424?q=80&w=600&auto=format&fit=crop'),
('Bakery', 'Fresh and seasonal bakery products', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=600&auto=format&fit=crop'),
('Meat', 'Fresh and seasonal meat products', 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?q=80&w=600&auto=format&fit=crop'),
('Seafood', 'Fresh and seasonal seafood products', 'https://images.unsplash.com/photo-1534482421-64566f976cfa?q=80&w=600&auto=format&fit=crop'),
('Snacks', 'Fresh and seasonal snacks', 'https://images.unsplash.com/photo-1599490659213-e2b9527b0876?q=80&w=600&auto=format&fit=crop'),
('Beverages', 'Fresh and seasonal beverages', 'https://images.unsplash.com/photo-1527960656366-ee2a999e32e6?q=80&w=600&auto=format&fit=crop'),
('Personal Care', 'Fresh and seasonal personal care products', 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?q=80&w=600&auto=format&fit=crop'),
('Household', 'Fresh and seasonal household products', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80&w=600&auto=format&fit=crop');

-- Insert sample admin users (password: password)
INSERT INTO admin_users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@freshmart.com', 'admin'),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@freshmart.com', 'manager');

-- Insert sample users (password: user123)
INSERT INTO users (name, email, password, phone, address) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', '123 Main St, City, State'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+0987654321', '456 Oak Ave, City, State'),
('Mike Johnson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1122334455', '789 Pine Rd, City, State');

-- Insert sample products
INSERT INTO products (name, description, price, stock_quantity, category_id, image_url) VALUES
('Fresh Apples', 'Sweet and juicy red apples', 2.99, 100, 1, 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?q=80&w=2070&auto=format&fit=crop'),
('Organic Bananas', 'Fresh organic bananas', 1.99, 150, 1, 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?q=80&w=2070&auto=format&fit=crop'),
('Fresh Tomatoes', 'Ripe red tomatoes', 3.49, 80, 2, 'https://images.unsplash.com/photo-1546094096-0df4bcaaa337?q=80&w=2070&auto=format&fit=crop'),
('Whole Milk', 'Fresh whole milk 1L', 2.49, 50, 3, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?q=80&w=2070&auto=format&fit=crop'),
('Fresh Bread', 'Artisan bread', 3.99, 30, 4, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=2070&auto=format&fit=crop'),
('Chicken Breast', 'Fresh chicken breast 500g', 8.99, 25, 5, 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?q=80&w=2070&auto=format&fit=crop'),
('Fresh Salmon', 'Atlantic salmon fillet', 12.99, 20, 6, 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=2070&auto=format&fit=crop'),
('Potato Chips', 'Classic potato chips', 2.99, 200, 7, 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?q=80&w=2070&auto=format&fit=crop'),
('Orange Juice', 'Fresh orange juice 1L', 4.49, 40, 8, 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?q=80&w=2070&auto=format&fit=crop'),
('Toothpaste', 'Mint toothpaste 100ml', 3.99, 100, 9, 'https://images.unsplash.com/photo-1559591935-c6c92c6c2b3e?q=80&w=2070&auto=format&fit=crop');
