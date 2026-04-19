<?php
require_once 'includes/config.php';
if(isLoggedIn()) redirect('pages/user_dashboard.php');

$error = '';
$success = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $mobile = sanitize($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if(strlen($password) < 6) {
        $error = 'Password must be at least 6 characters!';
    } elseif($password !== $confirm) {
        $error = 'Passwords do not match!';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if($check->fetch()) {
            $error = 'Email already registered!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $mobile, $hash]);
            $success = 'Registration successful! You can now login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Neer Nigrani</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="wave-container"><div class="wave"></div><div class="wave"></div></div>

<div class="auth-container">
    <div class="card auth-card fade-in">
        <div class="auth-header">
            <div class="auth-icon">🚀</div>
            <h2>Create Account</h2>
            <p>Register for Neer Nigrani</p>
        </div>
        
        <?php if($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="msg-success"><?= $success ?> <a href="login.php" style="color:var(--secondary);font-weight:600;">Login Now →</a></div><?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Aapka naam" required value="<?= sanitize($name ?? '') ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?= sanitize($email ?? '') ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Mobile Number</label>
                <input type="tel" name="mobile" class="form-control" placeholder="9876543210" required pattern="[0-9]{10}" value="<?= sanitize($mobile ?? '') ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
            </div>
            <button type="submit" class="btn btn-secondary" style="width:100%;">Create Account</button>
        </form>
        
        <p class="text-center mt-2" style="font-size:0.9rem;color:var(--text-muted);">
            Already have an account? <a href="login.php" style="color:var(--primary-light);">Login</a>
        </p>
        <p class="text-center" style="font-size:0.85rem;margin-top:8px;">
            <a href="index.php" style="color:var(--text-muted);">← Back to Home</a>
        </p>
    </div>
</div>

</body>
</html>
