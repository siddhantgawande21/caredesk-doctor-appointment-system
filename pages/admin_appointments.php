<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
requireAdmin();

require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {$aptId = (int)($_POST['appointment_id'] ?? 0);$newStatus = $_POST['new_status'] ?? '';$allowed = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

    if ($aptId > 0 && in_array($newStatus,$allowed, true)) {
        $stmt =$pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $aptId]);$updateMsg = "Appointment #$aptId status changed to$newStatus.";
    }
}

$totalPatients =$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient'")->fetchColumn();
$totalAppointments =$pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pendingAppointments =$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetchColumn();
$totalDoctors =$pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();

$query = "
    SELECT 
        a.id AS appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.symptoms,
        a.status,
        u.full_name AS patient_name,
        u.phone AS patient_phone,
        u.email AS patient_email,
        d.name AS doctor_name,
        d.specialty,
        d.room_no
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
";
$appointments = $pdo->query($query)->fetchAll();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h2>Admin Master Console</h2>
            <p>Real-time hospital booking oversight, patient inquiries, and status moderation.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Patients</h4>
            <p class="stat-number"><?= $totalPatients ?></p>
        </div>
        <div class="stat-card">
            <h4>Total Bookings</h4>
            <p class="stat-number"><?= $totalAppointments ?></p>
        </div>
        <div class="stat-card">
            <h4>Pending Action</h4>
            <p class="stat-number text-pending"><?= $pendingAppointments ?></p>
        </div>
        <div class="stat-card">
            <h4>Medical Staff</h4>
            <p class="stat-number"><?= $totalDoctors ?></p>
        </div>
    </div>

    <?php if (!empty($updateMsg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($updateMsg) ?></div>
    <?php endif; ?>

    <div class="table-card">
        <h3>All Registered Consultations</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Phone</th>
                        <th>Doctor</th>
                        <th>Date & Time</th>
                        <th>Symptoms</th>
                        <th>Status</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="8" class="text-center">No bookings logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as$apt): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($apt['appointment_id']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($apt['patient_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($apt['patient_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($apt['patient_phone']) ?></td>
                                <td><?= htmlspecialchars($apt['doctor_name']) ?> (<?= htmlspecialchars($apt['room_no']) ?>)</td>
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
                                            <option value="Pending" <?= $apt['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
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