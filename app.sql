CREATE DATABASE password_manager;

USE password_manager;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    fullname VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    recoveryKey CHAR(32) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    website VARCHAR(255) NOT NULL,
    encrypted_password TEXT NOT NULL,
    iv VARCHAR(32) NOT NULL, -- Stores IV as HEX (16 bytes)
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
