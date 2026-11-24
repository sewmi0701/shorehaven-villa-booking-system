-- IT1208 Coastal Villa Booking System — MySQL 8 / MariaDB schema and fictional seed data
-- Import using phpMyAdmin > Import, or: mysql -u root -p < db_structure.sql
-- WARNING: Demo accounts have known passwords. Change or remove them before public deployment.
CREATE DATABASE IF NOT EXISTS shorehaven_booking
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shorehaven_booking;

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    contact_number VARCHAR(24) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('guest','admin') NOT NULL DEFAULT 'guest',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rooms (
    room_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(120) NOT NULL,
    room_type ENUM('Suite','Deluxe','Family','Standard') NOT NULL,
    description TEXT NOT NULL,
    price_per_night DECIMAL(12,2) NOT NULL,
    max_guests TINYINT UNSIGNED NOT NULL,
    facilities VARCHAR(500) NOT NULL DEFAULT '',
    image_name ENUM('ocean','palm','garden','sunset','villa') NOT NULL DEFAULT 'ocean',
    room_status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (price_per_night > 0),
    CHECK (max_guests >= 1)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    room_id INT UNSIGNED NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(190) NOT NULL,
    guest_contact VARCHAR(24) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests TINYINT UNSIGNED NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    booking_status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    booking_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_booking_room FOREIGN KEY (room_id)
        REFERENCES rooms(room_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_room_reservations (room_id, booking_status, check_in, check_out),
    INDEX idx_guest_bookings (user_id, booking_date),
    CHECK (check_out > check_in),
    CHECK (guests >= 1),
    CHECK (total_amount >= 0)
) ENGINE=InnoDB;

-- For testing ONLY:
-- Administrator: admin@shorehaven.test / Admin@12345
-- Demo guest: demo@shorehaven.test / Guest@12345
-- The values below are PHP password_hash(PASSWORD_DEFAULT) bcrypt hashes.
INSERT IGNORE INTO users (user_id, name, email, contact_number, password, role) VALUES
(1, 'Villa Administrator', 'admin@shorehaven.test', '+94770000001', '$2y$12$4//g9O7PlRJIBO.8AEgLOuP7CEmQzlKvWWPkL7pusqIkm5GBEqqTS', 'admin'),
(2, 'Demo Guest', 'demo@shorehaven.test', '+94770000002', '$2y$12$OM4fkqU.QY0pyGnka31D.O5oO.mZn16EUT32o8rps8.MamL/rhvXi', 'guest');

INSERT IGNORE INTO rooms (room_id, room_name, room_type, description, price_per_night, max_guests, facilities, image_name, room_status) VALUES
(1, 'The Ocean Suite', 'Suite', 'An airy ocean-facing hideaway with an inviting king-size bed, soft natural details, and a private space to unwind after a day along the shore. A peaceful choice for travellers who want the sea close by.', 24500, 2, 'Ocean View, Wi-Fi, Air Conditioning, King Bed, Private Bathroom, Breakfast', 'ocean', 'active'),
(2, 'Palm Garden Deluxe', 'Deluxe', 'A calm and light-filled room set among the palms, with thoughtful coastal interiors and all the comforts for a restorative holiday. Open the windows and let the island breeze find you.', 18500, 2, 'Garden View, Wi-Fi, Air Conditioning, Queen Bed, Private Bathroom, Tea and Coffee', 'palm', 'active'),
(3, 'The Family Retreat', 'Family', 'A spacious coastal room designed for families and friends, with flexible sleeping space and room to enjoy the little moments together. Settle in, slow down, and make yourself at home.', 32000, 5, 'Garden View, Wi-Fi, Air Conditioning, Family Beds, Private Bathroom, Mini Fridge', 'garden', 'active'),
(4, 'Sunset Balcony Suite', 'Suite', 'An intimate retreat with warm finishes and an inviting balcony feel, made for golden evenings, quiet conversations, and easy mornings. A favourite setting for a romantic coastal getaway.', 28500, 2, 'Sunset View, Balcony, Wi-Fi, Air Conditioning, King Bed, Private Bathroom', 'sunset', 'active'),
(5, 'Coastal Comfort Room', 'Standard', 'A welcoming base for travellers exploring the southern coast, offering clean design, a restful bed and the essentials you need between beach visits and day trips.', 12500, 2, 'Wi-Fi, Air Conditioning, Queen Bed, Private Bathroom', 'ocean', 'active');

-- Two fictional bookings using dates relative to import day (future-dated sample records).
INSERT IGNORE INTO bookings (booking_id, user_id, room_id, guest_name, guest_email, guest_contact, check_in, check_out, guests, total_amount, booking_status) VALUES
(1, 2, 1, 'Demo Guest', 'demo@shorehaven.test', '+94770000002', DATE_ADD(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 17 DAY), 2, 73500, 'confirmed'),
(2, 2, 2, 'Demo Guest', 'demo@shorehaven.test', '+94770000002', DATE_ADD(CURDATE(), INTERVAL 28 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 2, 37000, 'pending');
