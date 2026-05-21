CREATE DATABASE IF NOT EXISTS db_horison;
USE db_horison;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    no_telp VARCHAR(20) NULL,
    gender ENUM('Laki-laki', 'Perempuan') NULL,
    tgl_lahir DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    gambar VARCHAR(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (nama_produk, harga, gambar) VALUES
('Coffe Soda', 25000, 'coffe_soda.jpg'),
('Lemon Wizz', 25000, 'lemon_wizz.jpg')
ON DUPLICATE KEY UPDATE nama_produk=VALUES(nama_produk);