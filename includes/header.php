<?php
header('Content-Type: text/html; charset=UTF-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareDesk - Hospital Appointment Portal</title>
    <link rel="stylesheet" href="/caredesk/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <a href="/caredesk/index.php" class="brand-logo">
                <span class="logo-icon">[+]</span> CareDesk
            </a>
            <nav class="nav-menu">
                <a href="/caredesk/index.php">Home</a>
                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'admin'): ?>
                        <a href="/caredesk/pages/admin_appointments.php" class="badge-link">Admin Console</a>
                    <?php elseif ($userRole === 'doctor'): ?>
                        <a href="/caredesk/pages/doctor_dashboard.php" class="badge-link">Doctor Portal</a>
                    <?php else: ?>
                        <a href="/caredesk/pages/dashboard.php">My Appointments</a>
                        <a href="/caredesk/pages/book_appointment.php" class="btn-nav">Book Appointment</a>
                    <?php endif; ?>
                    <span class="nav-user">Hello, <?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($userRole) ?>)</span>
                    <a href="/caredesk/pages/logout.php" class="nav-logout">Logout</a>
                <?php else: ?>
                    <a href="/caredesk/pages/login.php">Login</a>
                    <a href="/caredesk/pages/register.php" class="btn-nav">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="main-wrapper">
