CREATE DATABASE password_manager;

USE password_manager;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    recovery_key VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    site_name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    password TEXT NOT NULL,
    encryption_key VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
