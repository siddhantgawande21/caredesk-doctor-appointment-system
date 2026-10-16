<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
        $error = 'All fields are strictly required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Phone number must contain exactly 10 digits.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $pdo = getDBConnection();
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = 'An account with this email address already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'patient')");
            if ($insertStmt->execute([$fullName, $email, $phone, $hashedPassword])) {
                $success = 'Account created successfully! You can now sign in.';
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}
?>

<div class="auth-box">
    <h2>Patient Registration</h2>
    <p class="auth-sub">Create your account to book doctor consultations</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="/caredesk/pages/login.php">Login here</a></div>
    <?php endif; ?>

    <form action="" method="POST" class="auth-form" id="registerForm">
        <div class="form-group">
            <label for="full_name">Full Legal Name</label>
            <input type="text" name="full_name" id="full_name" required placeholder="e.g. Ramesh Kumar">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required placeholder="patient@example.com">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number (10 Digits)</label>
            <input type="tel" name="phone" id="phone" required placeholder="9876543210" maxlength="10">
        </div>

        <div class="form-group">
            <label for="password">Create Password</label>
            <input type="password" name="password" id="password" required placeholder="At least 6 characters">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Re-type password">
        </div>

        <button type="submit" class="btn-submit">Register Account</button>
    </form>
    <p class="auth-footer">Already registered? <a href="/caredesk/pages/login.php">Sign In</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>