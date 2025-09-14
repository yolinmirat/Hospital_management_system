<?php
// init_db.php
require_once 'config.php';

try {
    // Check if database exists, create if not
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        type ENUM('patient', 'doctor', 'admin') NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        specialization VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Appointments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        reason TEXT,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Medical history table
    $pdo->exec("CREATE TABLE IF NOT EXISTS medical_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        date DATE NOT NULL,
        diagnosis TEXT NOT NULL,
        prescription TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert sample data with hashed passwords
    $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
    
    // Check if sample users already exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert sample users
        $pdo->exec("INSERT INTO users (name, email, password, type, specialization) VALUES
            ('John Doe', 'patient@example.com', '$hashedPassword', 'patient', NULL),
            ('Dr. Smith', 'doctor@example.com', '$hashedPassword', 'doctor', 'Cardiology'),
            ('Admin User', 'admin@example.com', '$hashedPassword', 'admin', NULL)");
        
        // Insert sample appointments
        $pdo->exec("INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status) VALUES
            (1, 2, '2023-08-15', '10:00:00', 'Regular checkup', 'confirmed'),
            (1, 2, '2023-08-20', '11:30:00', 'Follow-up', 'pending')");
        
        // Insert sample medical history
        $pdo->exec("INSERT INTO medical_history (patient_id, date, diagnosis, prescription) VALUES
            (1, '2023-06-10', 'Hypertension', 'Medication A, 10mg daily'),
            (1, '2023-07-15', 'Common cold', 'Rest and fluids')");
            
        echo "Database initialized successfully! Sample data inserted.<br>";
    } else {
        echo "Database already exists with data.<br>";
    }
    
    echo "<a href='index.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage());
}
?>