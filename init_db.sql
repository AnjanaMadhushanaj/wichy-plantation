CSE coconut_shop_simple;


CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name REATE DATABASE IF NOT EXISTS coconut_shop_simple CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
UVARCHAR(100),
email VARCHAR(150) UNIQUE,
password VARCHAR(255)
);


CREATE TABLE product (
product_id INT AUTO_INCREMENT PRIMARY KEY,
product_name VARCHAR(150),
product_image VARCHAR(255),
product_price DECIMAL(10,2),
description TEXT
);


CREATE TABLE cart (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
product_id INT,
qty INT DEFAULT 1
);



CREATE TABLE orders (
	order_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT,
	user_email VARCHAR(150),
	product_id INT,
	product_name VARCHAR(150),
	qty INT,
	address VARCHAR(255),
	payment_method ENUM('COD','CARD'),
	status VARCHAR(50),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


