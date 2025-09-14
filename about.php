<?php
// about.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - MediCare System</title>
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
        
        .hero-section p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .features-section {
            padding: 3rem 0;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background-color: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .feature-icon i {
            font-size: 2rem;
            color: var(--primary);
        }
        
        .team-section {
            background-color: var(--light);
            padding: 4rem 0;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .team-member {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .team-member img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 4px solid var(--secondary);
        }
        
        .stats-section {
            padding: 4rem 0;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .stat-item {
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>About MediCare System</h1>
            <p>Revolutionizing healthcare management with cutting-edge technology and compassionate care</p>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Mission Section -->
            <section class="about-section">
                <h2>Our Mission</h2>
                <p>At MediCare, we are dedicated to transforming healthcare delivery through innovative technology solutions. Our mission is to make healthcare management seamless, efficient, and accessible to everyone.</p>
                
                <p>Founded in 2010, we have been at the forefront of medical technology, serving thousands of patients and healthcare professionals across the country.</p>
            </section>

            <!-- Features Section -->
            <section class="features-section">
                <h2>Why Choose MediCare?</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>24/7 Access</h3>
                        <p>Access your medical records and schedule appointments anytime, anywhere.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Secure & Private</h3>
                        <p>Your health data is protected with enterprise-grade security measures.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>Expert Doctors</h3>
                        <p>Connect with qualified healthcare professionals from various specialties.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Mobile Friendly</h3>
                        <p>Our platform works seamlessly on all devices - desktop, tablet, and mobile.</p>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="stats-section">
                <h2>Our Impact</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">10,000+</div>
                        <div class="stat-label">Patients Served</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Healthcare Professionals</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">50,000+</div>
                        <div class="stat-label">Appointments Booked</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Patient Satisfaction</div>
                    </div>
                </div>
            </section>

            <!-- Team Section -->
            <section class="team-section">
                <h2>Meet Our Leadership Team</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="feature-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Dr. Mirat Hasan Niloy</h3>
                        <p>Chief Medical Officer</p>
                        <p>Board-certified physician with 15+ years of experience</p>
                    </div>
                    
                    <div class="team-member">
                        <div class="feature-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3>Eng.Sagor Provakar</h3>
                        <p>Chief Technology Officer</p>
                        <p>Technology innovator with expertise in healthcare systems</p>
                    </div>
                    
                    <div class="team-member">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Mubtasim- Ur-Rahman</h3>
                        <p>Chief Operations Officer</p>
                        <p>Operations expert focused on patient experience</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>