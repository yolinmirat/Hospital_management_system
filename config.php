<?php
// config.php
session_start();

// Database configuration for XAMPP
$host = 'localhost';
$dbname = 'medicare_db';
$username = 'root';
$password = '';  // Default XAMPP password is empty

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Display error message
    die("Database connection failed: " . $e->getMessage());
}
?>