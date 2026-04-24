<?php
$host = 'localhost';
$user = 'root';
$pass = 'CDP17s1850913#^_^';

try {
    $conn = new PDO("mysql:host=$host", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS berita_db");
    echo "Database created successfully\n";

    // Use the database
    $conn->exec("USE berita_db");

    // Create Users Table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'kontributor', 'editor', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create Categories Table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    )";
    $conn->exec($sql);

    // Insert Default Categories
    $conn->exec("INSERT IGNORE INTO categories (name) VALUES ('Teknologi'), ('Olahraga'), ('Hiburan'), ('Politik')");

    // Create News Table
    $sql = "CREATE TABLE IF NOT EXISTS news (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        category_id INT(6) UNSIGNED,
        author_id INT(6) UNSIGNED,
        status ENUM('draft', 'pending', 'published') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);

    // Insert Default Users
    $password_admin = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@admin.com', '$password_admin', 'admin')");

    $password_kontributor = password_hash('kontributor123', PASSWORD_DEFAULT);
    $conn->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Kontributor', 'kontributor@admin.com', '$password_kontributor', 'kontributor')");

    $password_editor = password_hash('editor123', PASSWORD_DEFAULT);
    $conn->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Editor', 'editor@admin.com', '$password_editor', 'editor')");

    $password_user = password_hash('user123', PASSWORD_DEFAULT);
    $conn->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('User', 'user@admin.com', '$password_user', 'user')");

    echo "Tables & Default Users setup complete.\n";

} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>