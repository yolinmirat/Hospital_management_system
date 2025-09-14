<?php
// doctor.php
require_once 'config.php';

// Redirect to login if not authenticated or not a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: index.php");
    exit();
}

// Fetch doctor's details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$doctor = $stmt->fetch();

// Fetch doctor's appointments
$stmt = $pdo->prepare("SELECT a.*, p.name as patient_name 
                      FROM appointments a 
                      JOIN users p ON a.patient_id = p.id 
                      WHERE a.doctor_id = ? 
                      ORDER BY a.date, a.time");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

// Fetch today's appointments
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT a.*, p.name as patient_name 
                      FROM appointments a 
                      JOIN users p ON a.patient_id = p.id 
                      WHERE a.doctor_id = ? AND a.date = ?
                      ORDER BY a.time");
$stmt->execute([$_SESSION['user_id'], $today]);
$todayAppointments = $stmt->fetchAll();

// Fetch doctor's patients (unique patients who have appointments)
$stmt = $pdo->prepare("SELECT DISTINCT p.id, p.name, p.email, p.phone 
                      FROM users p 
                      JOIN appointments a ON p.id = a.patient_id 
                      WHERE a.doctor_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patients = $stmt->fetchAll();

// Count appointments by status
$appointmentStats = [
    'total' => count($appointments),
    'today' => count($todayAppointments),
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0
];

foreach ($appointments as $appt) {
    if ($appt['status'] === 'pending') $appointmentStats['pending']++;
    if ($appt['status'] === 'confirmed') $appointmentStats['confirmed']++;
    if ($appt['status'] === 'completed') $appointmentStats['completed']++;
}

// Handle appointment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $appointmentId = $_POST['appointment_id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        if ($stmt->execute([$status, $appointmentId, $_SESSION['user_id']])) {
            $success = "Appointment status updated successfully!";
            // Refresh the page to show updated status
            header("Location: doctor.php");
            exit();
        } else {
            $error = "Failed to update appointment status. Please try again.";
        }
    }
    elseif (isset($_POST['add_prescription'])) {
        $patientId = $_POST['patient_id'];
        $diagnosis = $_POST['diagnosis'];
        $prescription = $_POST['prescription'];
        $notes = $_POST['notes'];
        
        $stmt = $pdo->prepare("INSERT INTO medical_history (patient_id, date, diagnosis, prescription, notes) 
                              VALUES (?, CURDATE(), ?, ?, ?)");
        if ($stmt->execute([$patientId, $diagnosis, $prescription, $notes])) {
            $success = "Prescription added successfully!";
            // Refresh the page
            header("Location: doctor.php?tab=prescriptions");
            exit();
        } else {
            $error = "Failed to add prescription. Please try again.";
        }
    }
    elseif (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $specialization = $_POST['specialization'];
        $address = $_POST['address'];
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, specialization = ?, address = ? WHERE id = ?");
        if ($stmt->execute([$name, $phone, $specialization, $address, $_SESSION['user_id']])) {
            $success = "Profile updated successfully!";
            // Update session with new name
            $_SESSION['user_name'] = $name;
            // Refresh the page
            header("Location: doctor.php?tab=profile");
            exit();
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}

// Determine active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicare For Healthy Life - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
                    <h3>Doctor Dashboard</h3>
                    <ul>
                        <li><a href="doctor.php?tab=dashboard" class="<?php echo $tab === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="doctor.php?tab=schedule" class="<?php echo $tab === 'schedule' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Appointment Schedule</a></li>
                        <li><a href="doctor.php?tab=patients" class="<?php echo $tab === 'patients' ? 'active' : ''; ?>"><i class="fas fa-user-injured"></i> My Patients</a></li>
                        <li><a href="doctor.php?tab=prescriptions" class="<?php echo $tab === 'prescriptions' ? 'active' : ''; ?>"><i class="fas fa-prescription"></i> Prescriptions</a></li>
                        <li><a href="doctor.php?tab=profile" class="<?php echo $tab === 'profile' ? 'active' : ''; ?>"><i class="fas fa-user"></i> Profile</a></li>
                    </ul>
                </div>
                
                <!-- Main Content -->
                <div class="main-content">
                    <h2>Welcome, Dr. <?php echo $_SESSION['user_name']; ?></h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <!-- Dashboard Content Based on Selected Tab -->
                    <?php
                    switch ($tab) {
                        case 'schedule':
                            // Appointment Schedule Tab Content
                            ?>
                            <h3>Appointment Schedule</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?php echo $appointment['date']; ?></td>
                                                <td><?php echo $appointment['time']; ?></td>
                                                <td><?php echo $appointment['patient_name']; ?></td>
                                                <td><?php echo $appointment['reason']; ?></td>
                                                <td>
                                                    <span class="btn btn-<?php 
                                                        if ($appointment['status'] === 'confirmed') echo 'success';
                                                        elseif ($appointment['status'] === 'pending') echo 'warning';
                                                        elseif ($appointment['status'] === 'completed') echo 'primary';
                                                        else echo 'danger';
                                                    ?>">
                                                        <?php echo $appointment['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                        <select name="status" onchange="this.form.submit()" style="margin-right: 5px;">
                                                            <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="confirmed" <?php echo $appointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirm</option>
                                                            <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Complete</option>
                                                            <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancel</option>
                                                        </select>
                                                        <input type="hidden" name="update_status" value="1">
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            break;
                            
                        case 'patients':
                            // My Patients Tab Content
                            ?>
                            <h3>My Patients</h3>
                            <div class="card-grid">
                                <?php foreach ($patients as $patient): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-icon">
                                                <i class="fas fa-user-injured"></i>
                                            </div>
                                            <div class="card-title"><?php echo $patient['name']; ?></div>
                                        </div>
                                        <div class="card-desc">
                                            <p><strong>Email:</strong> <?php echo $patient['email']; ?></p>
                                            <p><strong>Phone:</strong> <?php echo $patient['phone']; ?></p>
                                            <p><strong>Last Visit:</strong> <?php echo date('M j, Y'); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            break;
                            
                        case 'prescriptions':
                            // Prescriptions Tab Content
                            ?>
                            <h3>Add New Prescription</h3>
                            <form method="POST" action="">
                                <input type="hidden" name="add_prescription" value="1">
                                <div class="form-group">
                                    <label for="patient-select">Patient</label>
                                    <select id="patient-select" name="patient_id" required>
                                        <option value="">Select a patient</option>
                                        <?php foreach ($patients as $patient): ?>
                                            <option value="<?php echo $patient['id']; ?>"><?php echo $patient['name']; ?> (<?php echo $patient['email']; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="diagnosis">Diagnosis</label>
                                    <textarea id="diagnosis" name="diagnosis" rows="3" placeholder="Enter diagnosis" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="prescription">Prescription</label>
                                    <textarea id="prescription" name="prescription" rows="3" placeholder="Enter prescription details" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea id="notes" name="notes" rows="2" placeholder="Additional notes"></textarea>
                                </div>
                                <button type="submit" class="btn">Add Prescription</button>
                            </form>
                            
                            <h3 style="margin-top: 30px;">Recent Prescriptions</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Patient</th>
                                            <th>Diagnosis</th>
                                            <th>Prescription</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Fetch recent prescriptions
                                        $stmt = $pdo->prepare("SELECT mh.*, p.name as patient_name 
                                                             FROM medical_history mh 
                                                             JOIN users p ON mh.patient_id = p.id 
                                                             WHERE mh.patient_id IN (
                                                                 SELECT DISTINCT patient_id FROM appointments WHERE doctor_id = ?
                                                             )
                                                             ORDER BY mh.date DESC 
                                                             LIMIT 5");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $prescriptions = $stmt->fetchAll();
                                        
                                        foreach ($prescriptions as $rx): ?>
                                            <tr>
                                                <td><?php echo $rx['date']; ?></td>
                                                <td><?php echo $rx['patient_name']; ?></td>
                                                <td><?php echo $rx['diagnosis']; ?></td>
                                                <td><?php echo $rx['prescription']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                                    <label for="doctor-name">Full Name</label>
                                    <input type="text" id="doctor-name" name="name" value="<?php echo $doctor['name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="doctor-email">Email Address</label>
                                    <input type="email" id="doctor-email" value="<?php echo $doctor['email']; ?>" disabled>
                                    <small style="color: var(--gray);">Email cannot be changed</small>
                                </div>
                                <div class="form-group">
                                    <label for="doctor-phone">Phone Number</label>
                                    <input type="tel" id="doctor-phone" name="phone" value="<?php echo $doctor['phone'] ?: ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="doctor-specialization">Specialization</label>
                                    <input type="text" id="doctor-specialization" name="specialization" value="<?php echo $doctor['specialization'] ?: ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="doctor-address">Clinic Address</label>
                                    <textarea id="doctor-address" name="address" rows="2"><?php echo $doctor['address'] ?: ''; ?></textarea>
                                </div>
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
                                        <div class="card-title">Today's Appointments</div>
                                    </div>
                                    <div class="card-value"><?php echo $appointmentStats['today']; ?></div>
                                    <div class="card-desc">You have <?php echo $appointmentStats['today']; ?> appointments today</div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-user-injured"></i>
                                        </div>
                                        <div class="card-title">Total Patients</div>
                                    </div>
                                    <div class="card-value"><?php echo count($patients); ?></div>
                                    <div class="card-desc">You have <?php echo count($patients); ?> patients</div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-prescription"></i>
                                        </div>
                                        <div class="card-title">Pending Appointments</div>
                                    </div>
                                    <div class="card-value"><?php echo $appointmentStats['pending']; ?></div>
                                    <div class="card-desc">You have <?php echo $appointmentStats['pending']; ?> pending appointments</div>
                                </div>
                            </div>
                            
                            <h3>Today's Appointments</h3>
                            <?php if (count($todayAppointments) > 0): ?>
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Reason</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($todayAppointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo $appointment['time']; ?></td>
                                                    <td><?php echo $appointment['patient_name']; ?></td>
                                                    <td><?php echo $appointment['reason']; ?></td>
                                                    <td>
                                                        <span class="btn btn-<?php 
                                                            if ($appointment['status'] === 'confirmed') echo 'success';
                                                            elseif ($appointment['status'] === 'pending') echo 'warning';
                                                            else echo 'danger';
                                                        ?>">
                                                            <?php echo $appointment['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form method="POST" action="" style="display: inline;">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <select name="status" onchange="this.form.submit()" style="margin-right: 5px;">
                                                                <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="confirmed" <?php echo $appointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirm</option>
                                                                <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Complete</option>
                                                                <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancel</option>
                                                            </select>
                                                            <input type="hidden" name="update_status" value="1">
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No appointments scheduled for today.</p>
                            <?php endif; ?>
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

    <script>
        // JavaScript for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handlers
            const profileForm = document.getElementById('doctor-profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Profile update functionality would be implemented here.');
                });
            }
        });
    </script>
</body>
</html>