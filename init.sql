CREATE DATABASE IF NOT EXISTS dev;

USE dev;

CREATE TABLE IF NOT EXISTS user (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    lastname VARCHAR(255),
    phone VARCHAR(25) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX(phone));

CREATE TABLE IF NOT EXISTS contact (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    contact_name VARCHAR(30) NOT NULL,
    contact_phone_number VARCHAR(25) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX(user_id),
    INDEX(contact_name),
    INDEX(contact_phone_number));