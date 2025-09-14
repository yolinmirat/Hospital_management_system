// dashboard.php
<?php
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Redirect based on user type
switch ($_SESSION['user_type']) {
    case 'patient':
        header("Location: patient.php");
        break;
    case 'doctor':
        header("Location: doctor.php");
        break;
    case 'admin':
        header("Location: admin.php");
        break;
    default:
        // Invalid user type, logout
        header("Location: logout.php");
        break;
}
exit();
?>