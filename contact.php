<?php
// contact.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - MediCare System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            padding: 4rem 0;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .hero-section h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .contact-container {
            display: flex;
            flex-direction: column;
            gap: 3rem;
            margin: 3rem 0;
        }
        
        .contact-info {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background-color: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .info-icon i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .info-content h3 {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .business-hours {
            margin-top: 2rem;
        }
        
        .hours-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .hours-table th,
        .hours-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .hours-table th {
            font-weight: 600;
            color: var(--dark);
        }
        
        .emergency-contact {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1.5rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We're here to help! Get in touch with our team</p>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="contact-container">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h2>Get In Touch</h2>
                    <p>Have questions or need assistance? Our team is here to help you.</p>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Visit Us</h3>
                            <p>123 Kuril<br>Healthcare City, HC 12345</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h3>Call Us</h3>
                            <p>+88018267878<br>+8801872284877 (Emergency)</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email Us</h3>
                            <p>info@medicare.com<br>support@medicare.com</p>
                        </div>
                    </div>
                    
                    <div class="business-hours">
                        <h3>Business Hours</h3>
                        <table class="hours-table">
                            <tr>
                                <th>Day</th>
                                <th>Hours</th>
                            </tr>
                            <tr>
                                <td>Monday - Friday</td>
                                <td>8:00 AM - 8:00 PM</td>
                            </tr>
                            <tr>
                                <td>Saturday</td>
                                <td>9:00 AM - 5:00 PM</td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>Emergency Services Only</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Emergency Contact -->
                <div class="emergency-contact">
                    <h3><i class="fas fa-exclamation-triangle"></i> Emergency Contact</h3>
                    <p>For medical emergencies, please call <strong>911</strong> immediately or visit your nearest emergency room.</p>
                    <p>For urgent medical advice after hours, call our emergency line: <strong>0177985069</strong></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>