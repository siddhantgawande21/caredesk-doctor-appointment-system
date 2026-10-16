<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
requireLogin();

require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name ASC")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = (int)($_POST['doctor_id'] ?? 0);
    $appointmentDate = trim($_POST['appointment_date'] ?? '');
    $appointmentTime = trim($_POST['appointment_time'] ?? '');
    $symptoms = trim($_POST['symptoms'] ?? '');

    $today = date('Y-m-d');

    if ($doctorId <= 0 || empty($appointmentDate) || empty($appointmentTime) || empty($symptoms)) {
        $error = 'All fields are mandatory.';
    } elseif ($appointmentDate < $today) {
        $error = 'Appointment date cannot be set in the past.';
    } else {
        // Double booking prevention
        $slotCheck = $pdo->prepare("
            SELECT id FROM appointments 
            WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'
        ");
        $slotCheck->execute([$doctorId, $appointmentDate, $appointmentTime]);

        if ($slotCheck->fetch()) {
            $error = 'The chosen doctor already has a booking for this time slot. Please choose another slot.';
        } else {
            $insert = $pdo->prepare("
                INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, symptoms, status)
                VALUES (?, ?, ?, ?, ?, 'Pending')
            ");
            if ($insert->execute([$_SESSION['user_id'], $doctorId, $appointmentDate, $appointmentTime, $symptoms])) {
                header("Location: /caredesk/pages/dashboard.php?msg=booked");
                exit;
            } else {
                $error = 'Failed to record booking. Please try again.';
            }
        }
    }
}
?>

<div class="form-container-card">
    <h2>Book Medical Consultation</h2>
    <p class="auth-sub">Select your specialist, preferred date and time, and describe your symptoms.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="auth-form" id="bookingForm">
        <div class="form-group">
            <label for="doctor_id">Select Doctor & Specialty</label>
            <select name="doctor_id" id="doctor_id" required>
                <option value="">-- Choose Doctor --</option>
                <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['specialty']) ?>) - Fee: â‚¹<?= number_format($d['fee'], 2) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group col-half">
                <label for="appointment_date">Consultation Date</label>
                <input type="date" name="appointment_date" id="appointment_date" min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group col-half">
                <label for="appointment_time">Consultation Slot</label>
                <select name="appointment_time" id="appointment_time" required>
                    <option value="">-- Select Time Slot --</option>
                    <option value="09:00:00">09:00 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="14:00:00">02:00 PM</option>
                    <option value="15:00:00">03:00 PM</option>
                    <option value="16:00:00">04:00 PM</option>
                    <option value="17:00:00">05:00 PM</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="symptoms">Symptoms / Reason for Visit</label>
            <textarea name="symptoms" id="symptoms" rows="4" required placeholder="Describe your health concern, symptoms, or checkup reason..."></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Confirm Booking</button>
            <a href="/caredesk/pages/dashboard.php" class="btn-secondary">Back to Dashboard</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>