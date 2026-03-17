-- ============================================================
-- Milda's Roses — Database Setup Script
-- Run this once in phpMyAdmin or MySQL CLI:
--   mysql -u root -p < setup_database.sql
-- ============================================================



-- -----------------------------------------------
-- USERS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    email       VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    phone       VARCHAR(50),
    role        ENUM('customer','admin') DEFAULT 'customer',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- ADDRESSES
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS addresses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    label       VARCHAR(100) DEFAULT 'Home',
    street      TEXT,
    city        VARCHAR(100),
    country     VARCHAR(100) DEFAULT 'Qatar',
    is_default  TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- -----------------------------------------------
-- CATEGORIES
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    slug  VARCHAR(100) NOT NULL UNIQUE
);

-- -----------------------------------------------
-- PRODUCTS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255) NOT NULL,
    description  TEXT,
    price        DECIMAL(10,2) NOT NULL,
    category_id  INT,
    image_path   VARCHAR(500),
    stock        INT DEFAULT 10,
    badge        VARCHAR(50),
    material     VARCHAR(100),
    rating       DECIMAL(2,1) DEFAULT 5.0,
    review_count INT DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- -----------------------------------------------
-- WISHLISTS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS wishlists (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- -----------------------------------------------
-- ORDERS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT,
    guest_name      VARCHAR(255),
    guest_email     VARCHAR(255),
    guest_phone     VARCHAR(50),
    shipping_street TEXT,
    shipping_city   VARCHAR(100),
    shipping_country VARCHAR(100) DEFAULT 'Qatar',
    shipping_method VARCHAR(100) DEFAULT 'standard',
    payment_method  VARCHAR(100) DEFAULT 'card',
    subtotal        DECIMAL(10,2),
    shipping_cost   DECIMAL(10,2) DEFAULT 25.00,
    vat             DECIMAL(10,2),
    discount        DECIMAL(10,2) DEFAULT 0.00,
    total           DECIMAL(10,2),
    status          ENUM('processing','confirmed','shipped','delivered','cancelled','refunded') DEFAULT 'processing',
    tracking_number VARCHAR(100),
    gift_message    TEXT,
    coupon_code     VARCHAR(50),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- -----------------------------------------------
-- ORDER ITEMS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    product_id INT,
    name       VARCHAR(255),
    price      DECIMAL(10,2),
    quantity   INT DEFAULT 1,
    color      VARCHAR(50),
    size       VARCHAR(50),
    FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- -----------------------------------------------
-- REVIEWS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id    INT,
    author     VARCHAR(255),
    rating     INT CHECK (rating BETWEEN 1 AND 5),
    body       TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE SET NULL
);

-- -----------------------------------------------
-- COUPONS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS coupons (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    code           VARCHAR(50) NOT NULL UNIQUE,
    discount_type  ENUM('percent','fixed') DEFAULT 'percent',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order      DECIMAL(10,2) DEFAULT 0,
    expires_at     DATE,
    uses_left      INT DEFAULT 100,
    active         TINYINT(1) DEFAULT 1
);

-- -----------------------------------------------
-- BLOG POSTS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS blog_posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    slug       VARCHAR(255) NOT NULL UNIQUE,
    excerpt    TEXT,
    body       LONGTEXT,
    tag        VARCHAR(100),
    author     VARCHAR(100) DEFAULT 'Milda\'s Roses',
    image_path VARCHAR(500),
    published  TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- SEED DATA
-- -----------------------------------------------

-- Admin user (password: Admin@1234)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@mildasroses.qa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Categories
INSERT INTO categories (name, slug) VALUES
('Bouquets',     'bouquets'),
('Wedding Décor','wedding-decor'),
('Gift Sets',    'gift-sets'),
('Custom Designs','custom-designs');

-- Products
INSERT INTO products (name, description, price, category_id, image_path, stock, badge, material, rating, review_count) VALUES
('Hot Pink Rose Basket',   'Handcrafted foam roses arranged in a charming popsicle-stick basket. A vibrant statement piece that lasts forever.', 285.00, 1, 'uploads/products/1.jpg', 12, 'Bestseller', 'Foam', 5.0, 48),
('Pink Paper Rose Garden', 'Delicate crepe paper roses clustered in a rustic ceramic pot. Ideal for desks, shelves, and gifting.', 180.00, 3, 'uploads/products/2.jpg', 8,  'New',        'Paper', 5.0, 32),
('Golden Tulip Bouquet',   'A generous bunch of golden paper tulips — sunshine in your home all year round.', 220.00, 1, 'uploads/products/3.jpg', 15, 'Trending',   'Paper', 4.0, 27),
('Orange Rose Vase',       'Vivid orange crepe paper roses presented in an elegant decorative vase. Perfect centrepiece.', 310.00, 1, 'uploads/products/4.jpg', 10, '',           'Fabric', 5.0, 41),
('Yellow Chrysanthemum Trio','Three stunning yellow paper chrysanthemums — bold, joyful, and everlasting.', 145.00, 3, 'uploads/products/11.jpg', 20, '',           'Paper', 5.0, 19),
('Wedding Rose Arch',      'Full custom rose arch arrangement for wedding ceremonies. Bespoke colours available.', 620.00, 2, 'uploads/products/1.jpg', 5,  'Custom',     'Fabric', 5.0, 14),
('Blush Garden Gift Box',  'Curated pink paper roses in a premium gift box. Perfect for any special occasion.', 195.00, 3, 'uploads/products/2.jpg', 9,  '',           'Paper', 4.0, 22),
('Sunflower Tulip Bundle', 'A mixed crepe paper bouquet of golden tulips and sunflowers — cheerful and bright.', 165.00, 1, 'uploads/products/3.jpg', 14, 'New',        'Paper', 5.0, 11),
('Orange Bloom Centrepiece','A striking orange rose centrepiece for dining tables and special events.', 280.00, 2, 'uploads/products/4.jpg', 7,  '',           'Fabric', 5.0, 30),
('Sunshine Daisy Bunch',   'Bright yellow paper daisies — a cheerful burst of colour for any room.', 120.00, 1, 'uploads/products/11.jpg', 18, '',           'Paper', 4.0, 16),
('Bridal Bouquet — Pink',  'Bespoke crepe rose bridal bouquet in soft pink tones. Made to order with love.', 580.00, 2, 'uploads/products/2.jpg', 4,  'Custom',     'Paper', 5.0, 38),
('Festive Flower Bundle',  'A mixed set of hot pink and golden paper flowers — ideal as a vibrant gift set.', 240.00, 3, 'uploads/products/1.jpg', 11, '',           'Mixed', 5.0, 25);

-- Sample coupon
INSERT INTO coupons (code, discount_type, discount_value, min_order, uses_left) VALUES
('BLOOM10', 'percent', 10.00, 100.00, 50),
('WELCOME20', 'fixed', 20.00, 150.00, 100);

-- Sample reviews
INSERT INTO reviews (product_id, author, rating, body) VALUES
(1, 'Sarah M.',  5, 'Absolutely stunning! The quality exceeded my expectations. Guests could not believe it was handmade paper!'),
(1, 'Amira K.',  5, 'Ordered for my anniversary and my husband loved it. The fact it will never wilt makes it so special.'),
(1, 'Noor F.',   4, 'Beautiful product, fast delivery, and very well packed. Would love a larger size option too.'),
(2, 'Fatima A.', 5, 'The pink roses are so realistic! Arrived in perfect condition. Will definitely order again.'),
(3, 'Dana R.',   5, 'Brightest bouquet I have ever seen. Makes the whole room feel sunny. Perfect gift for my mum.');

-- Blog posts
INSERT INTO blog_posts (title, slug, excerpt, tag, author, image_path) VALUES
('How to Make Paper Roses That Last a Lifetime', 'paper-roses-tutorial', 'Step-by-step guide to crafting stunning paper roses with just crepe paper, wire, and floral tape.', 'DIY Tutorial', 'Leila Hassan', 'uploads/products/2.jpg'),
('Planning Your Floral Décor: 2024 Wedding Trends', 'wedding-trends-2024', 'From dried pampas grass to preserved peony installations — the trends taking over weddings this year.', 'Wedding Guide', 'Maryam Khalil', 'uploads/products/3.jpg'),
('From Fabric to Flower: Inside Our Workshop', 'inside-our-workshop', 'A rare peek into how we transform ordinary fabric into extraordinary floral masterpieces.', 'Behind the Scenes', 'Noor Al-Sayed', 'uploads/products/4.jpg'),
('5 Ways to Style a Preserved Flower Arrangement', 'style-preserved-flowers', 'Preserved flowers can last years with the right care. Here are five stylish arrangement ideas.', 'DIY Tutorial', 'Leila Hassan', 'uploads/products/11.jpg'),
('Why Handmade Flowers Are a Greener Choice', 'eco-friendly-flowers', 'The cut flower industry has a significant carbon footprint. Learn how handmade alternatives help.', 'Sustainability', 'Leila Hassan', 'uploads/products/1.jpg'),
('The Perfect Floral Gift for Every Occasion', 'floral-gift-guide', 'Our curated guide to the best arrangements by occasion — birthdays, anniversaries, new babies and more.', 'Gift Guide', 'Maryam Khalil', 'uploads/products/2.jpg');
