<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();
$doctors =$pdo->query("SELECT * FROM doctors ORDER BY id ASC")->fetchAll();
?>

<section class="hero-banner">
    <div class="hero-content">
        <h1>Modern Healthcare Scheduling Made Simple</h1>
        <p>Book instant appointments with verified medical specialists, monitor consultation dates, and avoid clinic queues.</p>
        <div class="hero-actions">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/caredesk/pages/register.php" class="btn-primary">Create Patient Account</a>
                <a href="/caredesk/pages/login.php" class="btn-secondary">Sign In</a>
            <?php else: ?>
                <a href="/caredesk/pages/book_appointment.php" class="btn-primary">Book Consultation Now</a>
                <a href="/caredesk/pages/dashboard.php" class="btn-secondary">View Your Schedule</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-container">
    <div class="section-title">
        <h2>Available Medical Specialists</h2>
        <p>Select a doctor and schedule an in-person consultation.</p>
    </div>

    <div class="doctor-grid">
        <?php foreach ($doctors as$doc): ?>
            <div class="doctor-card">
                <div class="doc-avatar">Doctor</div>
                <h3><?= htmlspecialchars($doc['name']) ?></h3>
                <span class="doc-badge"><?= htmlspecialchars($doc['specialty']) ?></span>
                <p class="doc-info"><strong>Location:</strong> <?= htmlspecialchars($doc['room_no']) ?></p>
                <p class="doc-info"><strong>Schedule:</strong> <?= htmlspecialchars($doc['available_days']) ?></p>
                <p class="doc-fee">Rs. <?= number_format($doc['fee'], 2) ?> <span>/ Consultation</span></p>
                <a href="/caredesk/pages/book_appointment.php" class="btn-card">Book Visit</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
