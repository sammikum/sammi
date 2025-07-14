<?php
require_once 'config.php';

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    echo "Error creating database: " . $conn->error;
}

$conn->select_db(DB_NAME);

$users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    api_key VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($users_table) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

$api_usage_table = "CREATE TABLE IF NOT EXISTS api_usage (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED,
    image_name VARCHAR(255) NOT NULL,
    date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($api_usage_table) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

$admin_table = "CREATE TABLE IF NOT EXISTS admin (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($admin_table) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Function to get the database connection
function getDBConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
