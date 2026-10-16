<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
requireLogin();

$appointmentId = (int)($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

if ($appointmentId > 0) {
    $pdo = getDBConnection();
    if ($userRole === 'admin') {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
        $stmt->execute([$appointmentId]);
        header("Location: /caredesk/pages/admin_appointments.php?msg=updated");
    } else {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
        $stmt->execute([$appointmentId, $userId]);
        header("Location: /caredesk/pages/dashboard.php?msg=cancelled");
    }
    exit;
}

header("Location: /caredesk/pages/dashboard.php");
exit;