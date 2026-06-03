-- Hargeisa Village Restaurant Database Schema
-- Import via phpMyAdmin or: mysql -u root hargeisa_village < database/schema.sql

CREATE DATABASE IF NOT EXISTS hargeisa_village
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hargeisa_village;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    dietary_tags VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone_number VARCHAR(50),
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    num_guests INT NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'declined', 'seated', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    display_order INT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username admin — set password via install.php
INSERT INTO users (username, password_hash, email, role) VALUES
('admin', '$2y$12$u7R4i.bxB/a5gvplg54inuVc1ODudm1auAkrmJ//n3rtdoxCByCTW', 'admin@hargeisavillage.com', 'admin');

INSERT INTO settings (setting_key, setting_value, description) VALUES
('restaurant_name', 'Hargeisa Village Restaurant', 'Site name'),
('tagline', 'Authentic flavors in the heart of Hargeisa', 'Home hero tagline'),
('phone', '+252 63 000 0000', 'Contact phone'),
('email', 'info@hargeisavillage.com', 'Contact email'),
('address', 'Main Street, Hargeisa, Somaliland', 'Physical address'),
('facebook_url', '#', 'Facebook URL'),
('instagram_url', '#', 'Instagram URL'),
('twitter_url', '#', 'Twitter/X URL'),
('google_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3650!2d44.066!3d9.56!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwMzMnMzYuMCJOIDQ0wrAwMyc1OC4wIkU!5e0!3m2!1sen!2s!4v1', 'Google Maps embed URL'),
('notification_email', 'info@hargeisavillage.com', 'Reservation notification email'),
('hours_morning', '8:30 AM - 3:00 PM', 'Morning hours (all days)'),
('hours_afternoon', '4:30 PM - 11:00 PM', 'Afternoon hours (all days)'),
('hours_monday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Monday hours'),
('hours_tuesday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Tuesday hours'),
('hours_wednesday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Wednesday hours'),
('hours_thursday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Thursday hours'),
('hours_friday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Friday hours'),
('hours_saturday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Saturday hours'),
('hours_sunday', '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM', 'Sunday hours'),
('menu_pdf_url', '', 'PDF menu download URL'),
('footer_about', '', 'Footer description (empty = use tagline)'),
('footer_show_hours', '1', 'Show hours in footer (1=yes, 0=no)'),
('footer_copyright', '', 'Extra copyright text (optional)');

INSERT INTO pages (slug, title, content) VALUES
('home-intro', 'Welcome', '<p>Welcome to Hargeisa Village Restaurant, where tradition meets exceptional dining. We celebrate Somali cuisine with fresh ingredients, warm hospitality, and a welcoming atmosphere for families and friends.</p>'),
('about-story', 'Our Story', '<p>Hargeisa Village Restaurant was founded with a passion for sharing the rich culinary heritage of our region. From humble beginnings to becoming a beloved local destination, our journey has been guided by quality, community, and unforgettable flavors.</p>'),
('about-philosophy', 'Our Philosophy', '<p>We believe great food brings people together. Every dish is prepared with care, using locally sourced ingredients where possible, and served with the warmth that defines Somali hospitality.</p>'),
('about-ambiance', 'Ambiance', '<p>Our dining space blends modern comfort with cultural touches—soft lighting, comfortable seating, and an atmosphere perfect for casual lunches, family dinners, and special celebrations.</p>');

INSERT INTO menu_categories (name, description, display_order) VALUES
('Appetizers', 'Start your meal with our flavorful starters', 1),
('Main Courses', 'Hearty dishes showcasing our signature recipes', 2),
('Desserts', 'Sweet endings to your dining experience', 3),
('Drinks', 'Refreshing beverages and traditional drinks', 4);

INSERT INTO menu_items (category_id, name, description, price, dietary_tags, is_featured, is_available) VALUES
(1, 'Sambusa Trio', 'Crispy pastry filled with seasoned meat or lentils, served with chutney.', 8.50, 'spicy', 1, 1),
(1, 'Hummus & Flatbread', 'Creamy hummus with warm flatbread and olive oil drizzle.', 7.00, 'vegetarian', 0, 1),
(2, 'Village Lamb Platter', 'Slow-cooked lamb with rice, vegetables, and house sauce.', 18.99, '', 1, 1),
(2, 'Grilled Fish of the Day', 'Fresh catch grilled with herbs, lemon, and seasonal sides.', 16.50, 'gluten-free', 1, 1),
(3, 'Basbousa', 'Traditional semolina cake soaked in sweet syrup.', 6.00, 'vegetarian', 0, 1),
(4, 'Somali Tea', 'Spiced black tea with cardamom—served hot.', 3.50, 'vegan', 0, 1);
