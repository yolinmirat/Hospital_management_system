<?php
// patient.php
require_once 'config.php';

// Redirect to login if not authenticated or not a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: index.php");
    exit();
}

// Fetch patient's appointments
$stmt = $pdo->prepare("SELECT a.*, d.name as doctor_name 
                      FROM appointments a 
                      JOIN users d ON a.doctor_id = d.id 
                      WHERE a.patient_id = ? 
                      ORDER BY a.date, a.time");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

// Fetch patient's medical history
$stmt = $pdo->prepare("SELECT * FROM medical_history WHERE patient_id = ? ORDER BY date DESC");
$stmt->execute([$_SESSION['user_id']]);
$medicalHistory = $stmt->fetchAll();

// Fetch available doctors
$doctors = $pdo->query("SELECT * FROM users WHERE type = 'doctor'")->fetchAll();

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $doctorId = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];
    
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status) 
                          VALUES (?, ?, ?, ?, ?, 'pending')");
    if ($stmt->execute([$_SESSION['user_id'], $doctorId, $date, $time, $reason])) {
        $success = "Appointment booked successfully!";
        // Refresh to show the new appointment
        header("Location: patient.php");
        exit();
    } else {
        $error = "Failed to book appointment. Please try again.";
    }
}

// Check if additional columns exist in the users table
$columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
$hasDob = in_array('dob', $columns);
$hasAddress = in_array('address', $columns);
$hasPhone = in_array('phone', $columns);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $dob = isset($_POST['dob']) ? $_POST['dob'] : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    
    // Basic validation
    if (empty($name) || empty($email)) {
        $error = "Name and email are required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = "Email address is already registered to another account.";
        } else {
            // Build the update query based on available columns
            $updateFields = ["name = ?", "email = ?"];
            $params = [$name, $email];
            
            if ($hasPhone) {
                $updateFields[] = "phone = ?";
                $params[] = $phone;
            }
            
            if ($hasDob) {
                $updateFields[] = "dob = ?";
                $params[] = $dob;
            }
            
            if ($hasAddress) {
                $updateFields[] = "address = ?";
                $params[] = $address;
            }
            
            $params[] = $_SESSION['user_id'];
            
            $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute($params)) {
                $success = "Profile updated successfully!";
                
                // Update session variables
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                // Refresh page to show updated data
                header("Location: patient.php?tab=profile");
                exit();
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
    }
}

// Fetch current user data for profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch();

// Get upcoming appointments
$upcomingAppointments = array_filter($appointments, function($appt) {
    return strtotime($appt['date']) >= strtotime(date('Y-m-d'));
});

// Determine active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicare For Healthy Life - Patient Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hidden-field {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="dashboard">
                <!-- Sidebar -->
                <div class="sidebar">
                    <h3>Patient Dashboard</h3>
                    <ul>
                        <li><a href="patient.php?tab=dashboard" class="<?php echo $tab === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="patient.php?tab=appointments" class="<?php echo $tab === 'appointments' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                        <li><a href="patient.php?tab=history" class="<?php echo $tab === 'history' ? 'active' : ''; ?>"><i class="fas fa-history"></i> Medical History</a></li>
                        <li><a href="patient.php?tab=doctors" class="<?php echo $tab === 'doctors' ? 'active' : ''; ?>"><i class="fas fa-user-md"></i> Select Doctors</a></li>
                        <li><a href="patient.php?tab=profile" class="<?php echo $tab === 'profile' ? 'active' : ''; ?>"><i class="fas fa-user"></i> Profile</a></li>
                    </ul>
                </div>
                
                <!-- Main Content -->
                <div class="main-content">
                    <h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <!-- Dashboard Content Based on Selected Tab -->
                    <?php
                    switch ($tab) {
                        case 'appointments':
                            // Appointments Tab Content
                            ?>
                            <h3>My Appointments</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td>Dr. <?php echo $appointment['doctor_name']; ?></td>
                                                <td><?php echo $appointment['date']; ?></td>
                                                <td><?php echo $appointment['time']; ?></td>
                                                <td><?php echo $appointment['reason']; ?></td>
                                                <td><span class="btn btn-<?php echo $appointment['status'] === 'confirmed' ? 'success' : 'warning'; ?>"><?php echo $appointment['status']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div style="text-align: center; margin-top: 20px;">
                                <button class="btn" id="book-appointment-btn-2"><i class="fas fa-plus"></i> Book New Appointment</button>
                            </div>
                            <?php
                            break;
                            
                        case 'history':
                            // Medical History Tab Content
                            ?>
                            <h3>Medical History</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Diagnosis</th>
                                            <th>Prescription</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($medicalHistory as $record): ?>
                                            <tr>
                                                <td><?php echo $record['date']; ?></td>
                                                <td><?php echo $record['diagnosis']; ?></td>
                                                <td><?php echo $record['prescription']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            break;
                            
                        case 'doctors':
                            // Select Doctors Tab Content
                            ?>
                            <h3>Select Doctors</h3>
                            <div class="card-grid">
                                <?php foreach ($doctors as $doctor): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-icon">
                                                <i class="fas fa-user-md"></i>
                                            </div>
                                            <div class="card-title">Dr. <?php echo $doctor['name']; ?></div>
                                        </div>
                                        <div class="card-desc">
                                            <p><strong>Specialty:</strong> <?php echo $doctor['specialization']; ?></p>
                                            <p><strong>Email:</strong> <?php echo $doctor['email']; ?></p>
                                            <p><strong>Phone:</strong> <?php echo $doctor['phone']; ?></p>
                                        </div>
                                        <div style="margin-top: 15px;">
                                            <button class="btn btn-block book-doctor-btn" data-doctor-id="<?php echo $doctor['id']; ?>"><i class="fas fa-calendar"></i> Book Appointment</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            break;
                            
                        case 'profile':
                            // Profile Tab Content
                            ?>
                            <h3>My Profile</h3>
                            <form method="POST" action="">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="form-group">
                                    <label for="patient-name">Full Name</label>
                                    <input type="text" id="patient-name" name="name" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="patient-email">Email Address</label>
                                    <input type="email" id="patient-email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                </div>
                                
                                <?php if ($hasPhone): ?>
                                <div class="form-group">
                                    <label for="patient-phone">Phone Number</label>
                                    <input type="tel" id="patient-phone" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="phone" value="">
                                <?php endif; ?>
                                
                                <?php if ($hasDob): ?>
                                <div class="form-group">
                                    <label for="patient-dob">Date of Birth</label>
                                    <input type="date" id="patient-dob" name="dob" value="<?php echo htmlspecialchars($userData['dob'] ?? ''); ?>">
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="dob" value="">
                                <?php endif; ?>
                                
                                <?php if ($hasAddress): ?>
                                <div class="form-group">
                                    <label for="patient-address">Address</label>
                                    <textarea id="patient-address" name="address" rows="2"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="address" value="">
                                <?php endif; ?>
                                
                                <button type="submit" class="btn">Update Profile</button>
                            </form>
                            <?php
                            break;
                            
                        default:
                            // Default Dashboard Tab Content
                            ?>
                            <div class="card-grid">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="card-title">Upcoming Appointments</div>
                                    </div>
                                    <div class="card-value"><?php echo count($upcomingAppointments); ?></div>
                                    <div class="card-desc">You have <?php echo count($upcomingAppointments); ?> upcoming appointments</div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                        <div class="card-title">Available Doctors</div>
                                    </div>
                                    <div class="card-value"><?php echo count($doctors); ?></div>
                                    <div class="card-desc">There are <?php echo count($doctors); ?> doctors available</div>
                                </div>
                            </div>
                            
                            <h3>Recent Appointments</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($appointments, 0, 3) as $appointment): ?>
                                            <tr>
                                                <td>Dr. <?php echo $appointment['doctor_name']; ?></td>
                                                <td><?php echo $appointment['date']; ?></td>
                                                <td><?php echo $appointment['time']; ?></td>
                                                <td><?php echo $appointment['reason']; ?></td>
                                                <td><span class="btn btn-<?php echo $appointment['status'] === 'confirmed' ? 'success' : 'warning'; ?>"><?php echo $appointment['status']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div style="text-align: center; margin-top: 20px;">
                                <button class="btn" id="book-appointment-btn"><i class="fas fa-plus"></i> Book New Appointment</button>
                            </div>
                            <?php
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Appointment Modal -->
    <div id="appointment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Book Appointment</h3>
                <span class="close">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="book_appointment" value="1">
                <div class="form-group">
                    <label for="appointment-doctor">Doctor</label>
                    <select id="appointment-doctor" name="doctor_id" required>
                        <option value="">Select a doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>">Dr. <?php echo $doctor['name']; ?> (<?php echo $doctor['specialization']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointment-date">Date</label>
                    <input type="date" id="appointment-date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="appointment-time">Time</label>
                    <select id="appointment-time" name="time" required>
                        <option value="">Select a time</option>
                        <option value="09:00:00">09:00 AM</option>
                        <option value="10:00:00">10:00 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="14:00:00">02:00 PM</option>
                        <option value="15:00:00">03:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointment-reason">Reason</label>
                    <textarea id="appointment-reason" name="reason" rows="3" placeholder="Brief reason for appointment"></textarea>
                </div>
                <button type="submit" class="btn btn-block">Book Appointment</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript for modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('appointment-modal');
            const closeBtn = document.querySelector('.close');
            
            // Show modal when clicking any book appointment button
            const bookButtons = document.querySelectorAll('#book-appointment-btn, #book-appointment-btn-2, .book-doctor-btn');
            
            bookButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // If this is a doctor-specific button, pre-select that doctor
                    if (this.classList.contains('book-doctor-btn')) {
                        const doctorId = this.getAttribute('data-doctor-id');
                        document.getElementById('appointment-doctor').value = doctorId;
                    }
                    modal.style.display = 'flex';
                });
            });
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Set minimum date to today for appointment date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointment-date').setAttribute('min', today);
        });
    </script>
</body>
</html>