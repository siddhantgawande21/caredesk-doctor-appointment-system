<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
requireLogin();

require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();
$userId =$_SESSION['user_id'];

$stmt =$pdo->prepare("
    SELECT 
        a.id AS appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.symptoms,
        a.status,
        a.created_at,
        d.name AS doctor_name,
        d.specialty,
        d.room_no,
        d.fee
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->execute([$userId]);
$appointments =$stmt->fetchAll();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h2>Patient Dashboard</h2>
            <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>. Monitor and manage your medical consultations.</p>
        </div>
        <a href="/caredesk/pages/book_appointment.php" class="btn-primary">+ Book New Appointment</a>
    </div>

    <?php if (isset($_GET['msg']) &&$_GET['msg'] === 'booked'): ?>
        <div class="alert alert-success">Appointment scheduled successfully! Status is currently Pending review.</div>
    <?php elseif (isset($_GET['msg']) &&$_GET['msg'] === 'cancelled'): ?>
        <div class="alert alert-info">Appointment was successfully cancelled.</div>
    <?php endif; ?>

    <div class="table-card">
        <h3>My Scheduled Appointments (<?= count($appointments) ?>)</h3>
        <?php if (empty($appointments)): ?>
            <div class="empty-state">
                <p>No appointments booked yet.</p>
                <a href="/caredesk/pages/book_appointment.php" class="btn-secondary">Schedule Your First Consultation</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as$apt): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($apt['appointment_id']) ?></td>
                                <td><strong><?= htmlspecialchars($apt['doctor_name']) ?></strong></td>
                                <td><?= htmlspecialchars($apt['specialty']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($apt['appointment_date']))) ?></td>
                                <td><?= htmlspecialchars(date('h:i A', strtotime($apt['appointment_time']))) ?></td>
                                <td><?= htmlspecialchars($apt['room_no']) ?></td>
                                <td>Rs. <?= number_format($apt['fee'], 2) ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($apt['status']) ?>">
                                        <?= htmlspecialchars($apt['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($apt['status'] !== 'Cancelled' &&$apt['status'] !== 'Completed'): ?>
                                        <a href="/caredesk/pages/cancel_appointment.php?id=<?= $apt['appointment_id'] ?>" 
                                           class="btn-cancel" 
                                           onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                            Cancel
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
