-- ==========================================================
-- Bhakti Events & Celebrations (Event Management System)
-- Database Name: `events_db`
-- BCA Semester 5 Academic Project (Evaluator-Ready)
-- Student: Parmar Ankita Pankajbhai (24CS002UG01020)
-- Project Guide: Prof. Aryan More
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `events_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `events_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table (Admin & Customers)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('admin', 'user') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Categories Table
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(60) NOT NULL
) ENGINE=InnoDB;

-- 3. Events Table
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `venue` VARCHAR(255) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `ticket_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_seats` INT NOT NULL,
  `available_seats` INT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'default-event.jpg',
  `status` ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Bookings Table
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_reference` VARCHAR(20) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `ticket_quantity` INT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `booking_status` ENUM('Confirmed', 'Pending', 'Cancelled') DEFAULT 'Confirmed',
  `payment_status` ENUM('Paid', 'Unpaid') DEFAULT 'Paid',
  `payment_method` VARCHAR(50) DEFAULT 'Simulated Online (UPI/Card)',
  `booked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- 5. Seed Data
-- ==========================================================

-- Seed Users (Bcrypt hash for 'password123')
INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Admin Officer', 'admin@eventsphere.com', '$2y$10$eBRRtDLmX.rizqNmwx0FdutxTCjBB42As1niLpG5v8YIgaV4BsJPu', '9825661046', 'admin'),
(2, 'Het Raiyani', 'ankita@gmail.com', '$2y$10$eBRRtDLmX.rizqNmwx0FdutxTCjBB42As1niLpG5v8YIgaV4BsJPu', '9825661046', 'user'),
(3, 'Rajeshwari Jadeja', 'rajeshwari.jadeja@example.com', '$2y$10$eBRRtDLmX.rizqNmwx0FdutxTCjBB42As1niLpG5v8YIgaV4BsJPu', '9879112233', 'user'),
(4, 'Vikramaditya Solanki', 'vikram.solanki@example.com', '$2y$10$eBRRtDLmX.rizqNmwx0FdutxTCjBB42As1niLpG5v8YIgaV4BsJPu', '9724556677', 'user');

-- Seed Categories (Royal Indian Celebrations Taxonomy)
INSERT INTO `categories` (`id`, `category_name`, `slug`) VALUES
(1, 'Royal Weddings & Mandap', 'royal-weddings'),
(2, 'Live Sufi & Folk Concerts', 'sufi-folk-concerts'),
(3, 'Navratri & Cultural Garba', 'navratri-cultural-garba'),
(4, 'Sangeet & Celebrations', 'sangeet-celebrations'),
(5, 'Corporate Galas & Summits', 'corporate-galas'),
(6, 'College Youth Conclaves', 'college-youth-conclaves');

-- Seed Events (Authentic Celebrations & Experiential Events)
INSERT INTO `events` (`id`, `category_id`, `title`, `description`, `venue`, `event_date`, `event_time`, `ticket_price`, `total_seats`, `available_seats`, `image`, `status`) VALUES
(1, 1, 'Shahi Rajwadi Vivah - Royal Heritage Wedding Showcase', 'Experience an opulent celebration of regal Indian matrimony. Featuring live Shehnai and Sitar recitals, grand palace floral mandap crafted with thousands of fresh Dutch roses and fragrant marigolds, royal elephant welcome procession, and multi-cuisine Shahi Rajwadi feast at Udaipur lakefront.', 'Jagmandir Island Palace & Lawns, Udaipur, Rajasthan', '2026-11-20', '17:00:00', 1500.00, 350, 330, 'wedding.jpg', 'upcoming'),
(2, 2, 'Sur Taal: Soulful Sufi & Ghazal Mehfil Live', 'An enchanting open-air twilight musical mehfil featuring renowned Sufi vocalists, classical sitarists, and tabla virtuosos on traditional royal carpets illuminated by antique brass lanterns. Includes royal refreshments and artisan seating.', 'Sabarmati Riverfront Cultural Amphitheater, Ahmedabad', '2026-12-12', '19:00:00', 799.00, 500, 465, 'sufi.jpg', 'upcoming'),
(3, 3, 'Navratri Mega Rasleela Mahotsav 2026', 'Gujarat premier traditional heritage Garba celebration. 9 nights of non-stop energetic Raas-Garba with live orchestral folk artists, traditional Dhol beats, authentic Kathiyawadi food courts, and traditional costume competitions.', 'Heritage Palace Grounds, Gundala Road, Gondal, Gujarat', '2026-10-18', '20:30:00', 450.00, 1500, 1380, 'garba.jpg', 'upcoming'),
(4, 4, 'Royal Sangeet & Bollywood Symphony Gala', 'A glamorous night of high-energy family choreography, live Bollywood 40-piece symphony orchestra, illuminated crystal chandeliers, laser visual backdrop, and celebrity anchor hosting an unforgettable pre-wedding sangeet extravaganza.', 'The Grand Imperial Ballroom, SG Highway, Gandhinagar', '2026-11-28', '19:30:00', 1200.00, 300, 280, 'sangeet.jpg', 'upcoming'),
(5, 5, 'Corporate Excellence Leadership Gala & Banquet', 'The premier annual conclave celebrating industry visionaries, business innovators, and corporate leaders. Full-course five-star banquet dinner, high-profile keynote addresses, awards ceremony, and executive networking lounge.', 'Grand Hyatt Convention Ballroom, Santacruz, Mumbai', '2026-12-05', '18:30:00', 2500.00, 200, 175, 'gala.jpg', 'upcoming'),
(6, 6, 'Yuva Utsav: Inter-College Cultural & Music Conclave', 'The most electrifying university gathering featuring battle of collegiate music bands, classical dance fusion, digital design showcase, and student leadership summit with prominent guest performers and trophy awards.', 'University Central Auditorium, Pune Campus', '2026-11-10', '11:00:00', 250.00, 800, 720, 'youth-conclave.jpg', 'upcoming');

-- Seed Sample Bookings
INSERT INTO `bookings` (`id`, `booking_reference`, `user_id`, `event_id`, `ticket_quantity`, `total_amount`, `booking_status`, `payment_status`, `payment_method`, `booked_at`) VALUES
(1, 'EVT-ROYAL01', 2, 1, 2, 3000.00, 'Confirmed', 'Paid', 'UPI (Google Pay)', '2026-09-18 14:30:00'),
(2, 'EVT-SUFI02', 2, 2, 2, 1598.00, 'Confirmed', 'Paid', 'Simulated UPI', '2026-09-19 11:15:00'),
(3, 'EVT-GARBA03', 3, 3, 4, 1800.00, 'Confirmed', 'Paid', 'Net Banking', '2026-09-20 16:40:00'),
(4, 'EVT-SANGEET4', 4, 4, 2, 2400.00, 'Confirmed', 'Paid', 'Credit Card', '2026-09-20 18:20:00'),
(5, 'EVT-GALA05', 3, 5, 1, 2500.00, 'Pending', 'Unpaid', 'Pay at Counter', '2026-09-21 10:00:00');

-- 5. Inquiries Table (Client Royal Consultation Requests)
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `inquiry_ref` VARCHAR(30) NOT NULL UNIQUE,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(25) NOT NULL,
  `service` VARCHAR(100) NOT NULL,
  `event_date` VARCHAR(100) DEFAULT NULL,
  `venue` VARCHAR(150) DEFAULT NULL,
  `guests` VARCHAR(50) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('New', 'Contacted', 'Proposal Sent', 'Confirmed', 'Closed') DEFAULT 'New',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inquiries` (`id`, `inquiry_ref`, `fullname`, `email`, `phone`, `service`, `event_date`, `venue`, `guests`, `message`, `status`, `created_at`) VALUES
(1, 'INQ-2026-8812', 'Maharawal Pradyumansinh', 'pradyuman@rajputana.org', '9825661046', 'Royal Palace Destination Wedding (3 Days)', 'Winter 2026 / Nov 24', 'Heritage Lake Palace (Udaipur / Jaipur)', '500 - 1000 Guests', 'We require a turnkey 3-day royal matrimonial experience with live Shehnai, elephant cavalcade, and authentic Shahi Rajwadi dining.', 'New', '2026-09-20 14:10:00'),
(2, 'INQ-2026-7540', 'Ananya Mehta', 'ananya.mehta@gmail.com', '9879112233', 'Soulful Sufi & Classical Ghazal Mehfil', 'Dec 18, 2026', 'Riverfront Amphitheater (Ahmedabad)', '250 - 500 Guests', 'Interested in booking premier Sufi vocalists and sitarists for an open-air riverfront evening celebration with candlelight ambience.', 'Contacted', '2026-09-21 09:30:00'),
(3, 'INQ-2026-6198', 'Dr. Bhavesh Shah', 'dr.bhavesh@zydusmed.com', '9724556677', 'Corporate Leadership Banquet & Awards', 'Jan 15, 2027', '5-Star Luxury Hotel Ballroom (Gandhinagar / Mumbai)', '100 - 250 Guests', 'Annual healthcare leadership banquet requiring stage production, laser backdrop, audio engineering, and VIP dinner coordination.', 'Proposal Sent', '2026-09-21 11:45:00');

