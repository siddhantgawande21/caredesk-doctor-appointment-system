<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
requireLogin();

if ($_SESSION['user_role'] !== 'doctor') {
    header("Location: /caredesk/pages/dashboard.php?error=forbidden");
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Get this specific logged-in doctor's profile
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE user_id = ?");
$stmt->execute([$userId]);
$doctor = $stmt->fetch();

if (!$doctor) {
    die("<div class='alert alert-error'>No doctor profile is mapped to this user account.</div>");
}

$doctorId = $doctor['id'];

// Handle status updates made by this doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $aptId = (int)($_POST['appointment_id'] ?? 0);
    $newStatus = $_POST['new_status'] ?? '';
    $allowed = ['Confirmed', 'Completed', 'Cancelled'];

    if ($aptId > 0 && in_array($newStatus, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        $stmt->execute([$newStatus, $aptId, $doctorId]);
        $updateMsg = "Appointment #$aptId status updated to $newStatus.";
    }
}

// Fetch ONLY appointments booked for this specific doctor
$stmt = $pdo->prepare("
    SELECT 
        a.id AS appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.symptoms,
        a.status,
        u.full_name AS patient_name,
        u.phone AS patient_phone,
        u.email AS patient_email
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$doctorId]);
$appointments = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h2>Doctor Portal: <?= htmlspecialchars($doctor['name']) ?></h2>
            <p><strong>Department:</strong> <?= htmlspecialchars($doctor['specialty']) ?> | <strong>Room:</strong> <?= htmlspecialchars($doctor['room_no']) ?></p>
        </div>
    </div>

    <?php if (!empty($updateMsg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($updateMsg) ?></div>
    <?php endif; ?>

    <div class="table-card">
        <h3>My Scheduled Patient Visits (<?= count($appointments) ?>)</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Contact</th>
                        <th>Date & Time</th>
                        <th>Symptoms Reported</th>
                        <th>Current Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="7" class="text-center">No patients scheduled for you yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $apt): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($apt['appointment_id']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($apt['patient_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($apt['patient_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($apt['patient_phone']) ?></td>
                                <td>
                                    <?= htmlspecialchars(date('d M Y', strtotime($apt['appointment_date']))) ?><br>
                                    <small><?= htmlspecialchars(date('h:i A', strtotime($apt['appointment_time']))) ?></small>
                                </td>
                                <td><span class="truncate" title="<?= htmlspecialchars($apt['symptoms']) ?>"><?= htmlspecialchars($apt['symptoms']) ?></span></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($apt['status']) ?>">
                                        <?= htmlspecialchars($apt['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="" method="POST" class="inline-form">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="appointment_id" value="<?= $apt['appointment_id'] ?>">
                                        <select name="new_status" class="select-sm" onchange="this.form.submit()">
                                            <option value="">Update...</option>
                                            <option value="Confirmed" <?= $apt['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirm</option>
                                            <option value="Completed" <?= $apt['status'] === 'Completed' ? 'selected' : '' ?>>Complete</option>
                                            <option value="Cancelled" <?= $apt['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancel</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
