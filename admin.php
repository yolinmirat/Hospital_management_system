<?php
// admin.php
require_once 'config.php';

// Redirect to login if not authenticated or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

// Fetch all appointments with patient and doctor names
$appointments = $pdo->query("
    SELECT a.*, p.name as patient_name, d.name as doctor_name 
    FROM appointments a 
    JOIN users p ON a.patient_id = p.id 
    JOIN users d ON a.doctor_id = d.id 
    ORDER BY a.date DESC, a.time DESC
")->fetchAll();

// Count statistics
$userStats = [
    'total' => count($users),
    'patients' => 0,
    'doctors' => 0,
    'admins' => 0
];

$appointmentStats = [
    'total' => count($appointments),
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

foreach ($users as $user) {
    if ($user['type'] === 'patient') $userStats['patients']++;
    if ($user['type'] === 'doctor') $userStats['doctors']++;
    if ($user['type'] === 'admin') $userStats['admins']++;
}

foreach ($appointments as $appt) {
    if ($appt['status'] === 'pending') $appointmentStats['pending']++;
    if ($appt['status'] === 'confirmed') $appointmentStats['confirmed']++;
    if ($appt['status'] === 'completed') $appointmentStats['completed']++;
    if ($appt['status'] === 'cancelled') $appointmentStats['cancelled']++;
}

// Handle user management actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        // Add new user
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $type = $_POST['type'];
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $specialization = $_POST['specialization'] ?? '';
        
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "User with this email already exists.";
        } else {
            try {
                // Hash password and insert user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, type, phone, address, specialization) VALUES (?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$name, $email, $hashedPassword, $type, $phone, $address, $specialization])) {
                    $success = "User added successfully!";
                    // Refresh the page
                    header("Location: admin.php?tab=users");
                    exit();
                } else {
                    $error = "Failed to add user. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $userId = $_POST['user_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$userId])) {
                $success = "User deleted successfully!";
                // Refresh the page
                header("Location: admin.php?tab=users");
                exit();
            } else {
                $error = "Failed to delete user. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_appointment_status'])) {
        // Update appointment status
        $appointmentId = $_POST['appointment_id'];
        $status = $_POST['status'];
        
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            if ($stmt->execute([$status, $appointmentId])) {
                $success = "Appointment status updated successfully!";
                // Refresh the page
                header("Location: admin.php?tab=appointments");
                exit();
            } else {
                $error = "Failed to update appointment status. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_appointment'])) {
        // Delete appointment
        $appointmentId = $_POST['appointment_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
            if ($stmt->execute([$appointmentId])) {
                $success = "Appointment deleted successfully!";
                // Refresh the page
                header("Location: admin.php?tab=appointments");
                exit();
            } else {
                $error = "Failed to delete appointment. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
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
    <title>Medicare For Healthy Life - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
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
                    <h3>Admin Dashboard</h3>
                    <ul>
                        <li><a href="admin.php?tab=dashboard" class="<?php echo $tab === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="admin.php?tab=users" class="<?php echo $tab === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> User Management</a></li>
                        <li><a href="admin.php?tab=appointments" class="<?php echo $tab === 'appointments' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                        <li><a href="admin.php?tab=reports" class="<?php echo $tab === 'reports' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Reports & Analytics</a></li>
                        <li><a href="admin.php?tab=settings" class="<?php echo $tab === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> System Settings</a></li>
                    </ul>
                </div>
                
                <!-- Main Content -->
                <div class="main-content">
                    <h2>Welcome, <?php echo $_SESSION['user_name']; ?> (Admin)</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <!-- Dashboard Content Based on Selected Tab -->
                    <?php
                    switch ($tab) {
                        case 'users':
                            // User Management Tab Content
                            ?>
                            <h3>User Management</h3>
                            
                            <div class="card" style="margin-bottom: 20px;">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="card-title">Add New User</div>
                                </div>
                                <div class="card-desc">
                                    <form method="POST" action="">
                                        <input type="hidden" name="add_user" value="1">
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                            <div class="form-group">
                                                <label for="name">Full Name *</label>
                                                <input type="text" id="name" name="name" placeholder="Enter full name" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email Address *</label>
                                                <input type="email" id="email" name="email" placeholder="Enter email address" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password *</label>
                                                <input type="password" id="password" name="password" placeholder="Enter password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="type">User Type *</label>
                                                <select id="type" name="type" required>
                                                    <option value="">Select user type</option>
                                                    <option value="patient">Patient</option>
                                                    <option value="doctor">Doctor</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Phone Number</label>
                                                <input type="tel" id="phone" name="phone" placeholder="Enter phone number">
                                            </div>
                                            <div class="form-group">
                                                <label for="specialization">Specialization (Doctors only)</label>
                                                <input type="text" id="specialization" name="specialization" placeholder="Enter specialization">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" rows="2" placeholder="Enter address"></textarea>
                                        </div>
                                        <button type="submit" class="btn">Add User</button>
                                    </form>
                                </div>
                            </div>
                            
                            <h3>All Users</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                            <th>Phone</th>
                                            <th>Specialization</th>
                                            <th>Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo $user['name']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td><span class="btn btn-<?php 
                                                    if ($user['type'] === 'admin') echo 'danger';
                                                    elseif ($user['type'] === 'doctor') echo 'primary';
                                                    else echo 'success';
                                                ?>"><?php echo $user['type']; ?></span></td>
                                                <td><?php echo $user['phone'] ?: 'N/A'; ?></td>
                                                <td><?php echo $user['specialization'] ?: 'N/A'; ?></td>
                                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                                <td class="action-buttons">
                                                    <button class="btn"><i class="fas fa-edit"></i> Edit</button>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="delete_user" value="1">
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash"></i> Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            break;
                            
                        case 'appointments':
                            // Appointments Tab Content
                            ?>
                            <h3>Appointment Management</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?php echo $appointment['id']; ?></td>
                                                <td><?php echo $appointment['patient_name']; ?></td>
                                                <td>Dr. <?php echo $appointment['doctor_name']; ?></td>
                                                <td><?php echo $appointment['date']; ?></td>
                                                <td><?php echo $appointment['time']; ?></td>
                                                <td><?php echo $appointment['reason']; ?></td>
                                                <td>
                                                    <span class="btn btn-<?php 
                                                        if ($appointment['status'] === 'confirmed') echo 'success';
                                                        elseif ($appointment['status'] === 'pending') echo 'warning';
                                                        elseif ($appointment['status'] === 'completed') echo 'primary';
                                                        else echo 'danger';
                                                    ?>"><?php echo $appointment['status']; ?></span>
                                                </td>
                                                <td class="action-buttons">
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                        <select name="status" onchange="this.form.submit()" style="margin-right: 5px;">
                                                            <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="confirmed" <?php echo $appointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirm</option>
                                                            <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Complete</option>
                                                            <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancel</option>
                                                        </select>
                                                        <input type="hidden" name="update_appointment_status" value="1">
                                                    </form>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                        <input type="hidden" name="delete_appointment" value="1">
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?')"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            break;
                            
                        case 'reports':
                            // Reports & Analytics Tab Content
                            ?>
                            <h3>Reports & Analytics</h3>
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <i class="fas fa-users fa-2x" style="color: var(--primary);"></i>
                                    <div class="stat-number"><?php echo $userStats['total']; ?></div>
                                    <div class="stat-label">Total Users</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-user-injured fa-2x" style="color: var(--success);"></i>
                                    <div class="stat-number"><?php echo $userStats['patients']; ?></div>
                                    <div class="stat-label">Patients</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-user-md fa-2x" style="color: var(--primary);"></i>
                                    <div class="stat-number"><?php echo $userStats['doctors']; ?></div>
                                    <div class="stat-label">Doctors</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-user-shield fa-2x" style="color: var(--danger);"></i>
                                    <div class="stat-number"><?php echo $userStats['admins']; ?></div>
                                    <div class="stat-label">Admins</div>
                                </div>
                            </div>
                            
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <i class="fas fa-calendar-check fa-2x" style="color: var(--primary);"></i>
                                    <div class="stat-number"><?php echo $appointmentStats['total']; ?></div>
                                    <div class="stat-label">Total Appointments</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-clock fa-2x" style="color: var(--warning);"></i>
                                    <div class="stat-number"><?php echo $appointmentStats['pending']; ?></div>
                                    <div class="stat-label">Pending</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-check-circle fa-2x" style="color: var(--success);"></i>
                                    <div class="stat-number"><?php echo $appointmentStats['confirmed']; ?></div>
                                    <div class="stat-label">Confirmed</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-times-circle fa-2x" style="color: var(--danger);"></i>
                                    <div class="stat-number"><?php echo $appointmentStats['cancelled']; ?></div>
                                    <div class="stat-label">Cancelled</div>
                                </div>
                            </div>
                            
                            <h3>Recent Activity</h3>
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i'); ?></td>
                                            <td>Admin User</td>
                                            <td>System Login</td>
                                            <td>Admin logged into the system</td>
                                        </tr>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime('-1 hour')); ?></td>
                                            <td>Dr. Smith</td>
                                            <td>Appointment Confirmed</td>
                                            <td>Confirmed appointment with John Doe</td>
                                        </tr>
                                        <tr>
                                            <td><?php echo date('Y-m-d', strtotime('-1 day')); ?></td>
                                            <td>John Doe</td>
                                            <td>New Appointment</td>
                                            <td>Booked appointment with Dr. Johnson</td>
                                        </tr>
                                        <tr>
                                            <td><?php echo date('Y-m-d', strtotime('-2 days')); ?></td>
                                            <td>Admin User</td>
                                            <td>User Management</td>
                                            <td>Added new doctor: Dr. Williams</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            break;
                            
                        case 'settings':
                            // System Settings Tab Content
                            ?>
                            <h3>System Settings</h3>
                            
                            <div class="card" style="margin-bottom: 20px;">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="card-title">General Settings</div>
                                </div>
                                <div class="card-desc">
                                    <form method="POST" action="">
                                        <input type="hidden" name="update_settings" value="general">
                                        <div class="form-group">
                                            <label for="system-name">System Name</label>
                                            <input type="text" id="system-name" name="system_name" value="Medicare For Healthy Life">
                                        </div>
                                        <div class="form-group">
                                            <label for="admin-email">Admin Email</label>
                                            <input type="email" id="admin-email" name="admin_email" value="admin@example.com">
                                        </div>
                                        <div class="form-group">
                                            <label for="timezone">Timezone</label>
                                            <select id="timezone" name="timezone">
                                                <option value="UTC">UTC</option>
                                                <option value="EST" selected>Eastern Time (EST)</option>
                                                <option value="PST">Pacific Time (PST)</option>
                                                <option value="CST">Central Time (CST)</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn">Save General Settings</button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="card-title">Security Settings</div>
                                </div>
                                <div class="card-desc">
                                    <form method="POST" action="">
                                        <input type="hidden" name="update_settings" value="security">
                                        <div class="form-group">
                                            <label for="session-timeout">Session Timeout (minutes)</label>
                                            <input type="number" id="session-timeout" name="session_timeout" value="30" min="5">
                                        </div>
                                        <div class="form-group">
                                            <label for="password-policy">Password Policy</label>
                                            <select id="password-policy" name="password_policy">
                                                <option value="low">Low (6+ characters)</option>
                                                <option value="medium" selected>Medium (8+ characters, letters & numbers)</option>
                                                <option value="high">High (10+ characters, mixed case, numbers & symbols)</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="two_factor" checked> Enable Two-Factor Authentication
                                            </label>
                                        </div>
                                        <button type="submit" class="btn">Save Security Settings</button>
                                    </form>
                                </div>
                            </div>
                            <?php
                            break;
                            
                        default:
                            // Default Dashboard Tab Content
                            ?>
                            <h3>System Overview</h3>
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <i class="fas fa-users fa-2x" style="color: var(--primary);"></i>
                                    <div class="stat-number"><?php echo $userStats['total']; ?></div>
                                    <div class="stat-label">Total Users</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-calendar-check fa-2x" style="color: var(--success);"></i>
                                    <div class="stat-number"><?php echo $appointmentStats['total']; ?></div>
                                    <div class="stat-label">Total Appointments</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-user-md fa-2x" style="color: var(--accent);"></i>
                                    <div class="stat-number"><?php echo $userStats['doctors']; ?></div>
                                    <div class="stat-label">Doctors</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-user-injured fa-2x" style="color: var(--warning);"></i>
                                    <div class="stat-number"><?php echo $userStats['patients']; ?></div>
                                    <div class="stat-label">Patients</div>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
                                <div>
                                    <h3>Recent Users</h3>
                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Type</th>
                                                    <th>Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($users, 0, 5) as $user): ?>
                                                    <tr>
                                                        <td><?php echo $user['name']; ?></td>
                                                        <td><?php echo $user['email']; ?></td>
                                                        <td><span class="btn btn-<?php 
                                                            if ($user['type'] === 'admin') echo 'danger';
                                                            elseif ($user['type'] === 'doctor') echo 'primary';
                                                            else echo 'success';
                                                        ?>"><?php echo $user['type']; ?></span></td>
                                                        <td><?php echo date('M j', strtotime($user['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3>Recent Appointments</h3>
                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Patient</th>
                                                    <th>Doctor</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($appointments, 0, 5) as $appointment): ?>
                                                    <tr>
                                                        <td><?php echo $appointment['patient_name']; ?></td>
                                                        <td>Dr. <?php echo $appointment['doctor_name']; ?></td>
                                                        <td><?php echo $appointment['date']; ?></td>
                                                        <td><span class="btn btn-<?php 
                                                            if ($appointment['status'] === 'confirmed') echo 'success';
                                                            elseif ($appointment['status'] === 'pending') echo 'warning';
                                                            else echo 'danger';
                                                        ?>"><?php echo $appointment['status']; ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
</body>
</html>