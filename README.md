🏥 Medicare- Hospital Management System

A comprehensive, role-based healthcare management system built with PHP and MySQL that streamlines medical operations and improves patient care through specialized dashboards for patients, doctors, and administrators.

## 🌟 Features

## 👥 Role-Based Access Control
- Patients: Book appointments, view medical history, select doctors, manage profiles
- Doctors: Manage appointments, update patient records, create prescriptions, track schedules
- Administrators: Full system control, user management, analytics, and reporting

## 💻 Technical Features
- Secure authentication system with password hashing
- Responsive design with clean, professional UI
- Appointment management with status tracking
- Medical history and prescription records
- Comprehensive admin dashboard with statistics
- Database initialization with sample data

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL (with PDO)
- **Styling**: Custom CSS with Font Awesome icons
- **Server**: XAMPP compatible

## 📁 Project Structure
medicare-system
├── config.php              # Database configuration and session management<br>
├── index.php               # Login page with authentication<br>
├── register.php            # User registration system<br>
├── dashboard.php           # Role-based routing<br>
├── patient.php             # Patient dashboard<br>
├── doctor.php              # Doctor dashboard<br>
├── admin.php               # Administrator dashboard<br>
├── about.php               # About us page<br>
├── contact.php             # Contact information<br>
├── init_db.php             # Database initialization<br>
├── logout.php              # Session termination<br>
├── header.php              # Consistent header<br>
├── footer.php              # Consistent footer<br>
├── style.css               # Comprehensive styling<br>
└── README.md               # This file<br>

## 🚀 Installation & Setup

## Prerequisites
- XAMPP/WAMP/MAMP server installed
- Web browser with JavaScript enabled

### Installation Steps
1. Clone or download the project files
2. Place the project folder in your server's root directory (e.g., htdocs for XAMPP)
3. Start Apache and MySQL services in your XAMPP Control Panel
4. Open your browser and navigate to "http://localhost/phpmyadmin"
5. Create a new database named "medicare_db"
6. Navigate to "http://localhost/your-project-folder/init_db.php" to initialize the database with sample data
7. Access the application at "http://localhost/your-project-folder/index.php"

## Default Login Credentials
After initialization, use these credentials to login:

**Patient:**
- Email: patient@example.com
- Password: password
- Role: Patient

**Doctor:**
- Email: doctor@example.com
- Password: password
- Role: Doctor

**Administrator:**
- Email: admin@example.com
- Password: password
- Role: Admin

## 📊 Database Schema

The system uses three main tables:
- **users**: Stores patient, doctor, and admin information
- **appointments**: Manages appointment scheduling and status
- **medical_history**: Tracks patient diagnoses and prescriptions

## 🎯 Usage Guide

### For Patients:
1. Register or login with patient credentials
2. Book appointments with available doctors
3. View medical history and appointment status
4. Update personal profile information

### For Doctors:
1. Login with doctor credentials
2. View and manage appointment schedule
3. Update appointment statuses
4. Create prescriptions for patients
5. Maintain profile information

### For Administrators:
1. Login with admin credentials
2. Manage all users (add, edit, delete)
3. Oversee all appointments
4. View system analytics and reports
5. Configure system settings

## 🔒 Security Features

- Password hashing using PHP's password_hash()
- Session-based authentication
- Role-based access control
- SQL injection prevention with PDO prepared statements
- Input validation on forms

## 🎨 Customization

The system can be customized by:
- Modifying the color scheme in `style.css` (CSS variables)
- Adding new fields to database tables
- Extending functionality in respective role-based files
- Customizing the UI in HTML/PHP files

## 📈 Future Enhancements

Potential improvements for the system:
- Email integration for appointment reminders
- Payment gateway integration
- Advanced reporting and analytics
- Mobile-responsive improvements
- Real-time chat functionality
- Electronic health records (EHR) integration

## 🆘 Support

For support or questions about this healthcare management system, please create an issue in the repository or contact the development team.

##  Acknowledgments

- Font Awesome for icons
- PHP and MySQL communities for documentation
- XAMPP for local server environment

Medicare: For Healthy Life - Revolutionizing healthcare management through technology and compassionate care.
