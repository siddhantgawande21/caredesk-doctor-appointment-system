<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';

if (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $error = 'Please sign in to access that page.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: /caredesk/pages/admin_appointments.php");
            } elseif ($user['role'] === 'doctor') {
                header("Location: /caredesk/pages/doctor_dashboard.php");
            } else {
                header("Location: /caredesk/pages/dashboard.php");
            }
            exit;
        } else {
            $error = 'Invalid credentials. Please verify your email and password.';
        }
    }
}
?>

<div class="auth-box">
    <h2>Account Login</h2>
    <p class="auth-sub">Access your consultations, schedule, and profile</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Registered Email</label>
            <input type="email" name="email" id="email" required placeholder="name@domain.com">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter password">
        </div>

        <button type="submit" class="btn-submit">Sign In</button>
    </form>

    <div class="demo-credentials">
        <strong>Test Logins (Password: <code>password</code>):</strong>
        <p>- Admin: <code>admin@caredesk.com</code></p>
        <p>- Dr. Aisha: <code>aisha@caredesk.com</code></p>
        <p>- Dr. Vikram: <code>vikram@caredesk.com</code></p>
        <p>- Dr. Neha: <code>neha@caredesk.com</code></p>
        <p>- Dr. Rajesh: <code>rajesh@caredesk.com</code></p>
        <p>- Patient: <code>patient@caredesk.com</code></p>
    </div>

    <p class="auth-footer">Don't have an account? <a href="/caredesk/pages/register.php">Create Account</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
