CREATE TABLE bookings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    booth_id INT(11) NOT NULL,
    zone_id INT(11) NOT NULL,
    booking_date DATE NOT NULL,
    payment_due_date DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'expired', 'pending payment') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    details VARCHAR(300),
    price DECIMAL(10,2) NOT NULL,
    event_id INT(11),
    PRIMARY KEY (id)
);
