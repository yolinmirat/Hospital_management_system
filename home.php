<?php
// home.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare System - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1932&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero-section p {
            font-size: 1.5rem;
            max-width: 800px;
            margin: 0 auto 2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .features-section {
            padding: 4rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--primary);
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
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
        
        .testimonials-section {
            background-color: var(--light);
            padding: 4rem 0;
        }
        
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 1rem;
        }
        
        .testimonial-author {
            font-weight: bold;
            color: var(--primary);
        }
        
        .cta-section {
            padding: 4rem 0;
            text-align: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Your Health, Our Priority</h1>
            <p>Experience seamless healthcare management with MediCare's advanced platform</p>

        </div>
    </section>

    <!-- Main Content -->
    <main>
        

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <div class="container">
                <h2 class="section-title">What Our Patients Say</h2>
                <div class="testimonial-grid">
                    <div class="testimonial-card">
                        <p class="testimonial-text">"MediCare has transformed how I manage my health. Booking appointments is so convenient, and I can access my medical history anytime."</p>
                        <p class="testimonial-author">- Sarah Ali</p>
                    </div>
                    
                    <div class="testimonial-card">
                        <p class="testimonial-text">"As a doctor, MediCare has streamlined my practice. I can easily manage my schedule and focus more on patient care."</p>
                        <p class="testimonial-author">- Dr. Pran Gopal</p>
                    </div>
                    
                    <div class="testimonial-card">
                        <p class="testimonial-text">"The platform is intuitive and user-friendly. I appreciate how it keeps all my health information organized in one place."</p>
                        <p class="testimonial-author">- Robert Williams</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Ready to Take Control of Your Healthcare?</h2>
                <p>Join thousands of satisfied patients and healthcare professionals using MediCare</p>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-light btn-large">Get Started Today</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn btn-light btn-large">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>