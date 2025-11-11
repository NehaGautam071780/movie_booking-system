DROP DATABASE IF EXISTS movie_booking;
USE movie_booking;

CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    seats INT NOT NULL,
    seat_numbers VARCHAR(255) DEFAULT '',
    total_amount VARCHAR(255) DEFAULT '0',
    booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);

INSERT INTO users (email, password, role) VALUES
('admin@123','admin123','admin'),
('user@123','user123','user'),
('nneehhaaggaauuttaamm@gmail.com','neha123','user');
