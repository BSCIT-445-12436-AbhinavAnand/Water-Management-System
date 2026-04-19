<?php
require_once 'includes/config.php';
if(isLoggedIn()) redirect('pages/user_dashboard.php');
if(!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) redirect('forgot_password.php');

$email = $_SESSION['reset_email'];
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if(strlen($password) < 6) {
        $error = 'Password must be at least 6 characters!';
    } elseif($password !== $confirm) {
        $error = 'Passwords do not match!';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);
        
        // Mark OTP as used
        $pdo->prepare("UPDATE password_resets SET is_used = 1 WHERE email = ?")->execute([$email]);
        
        // Clear session
        unset($_SESSION['reset_email'], $_SESSION['otp_verified'], $_SESSION['debug_otp']);
        
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Neer Nigrani</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="wave-container"><div class="wave"></div><div class="wave"></div></div>

<div class="auth-container">
    <div class="card auth-card fade-in">
        <?php if($success): ?>
            <div class="auth-header">
                <div class="auth-icon" style="font-size:3rem;">✅</div>
                <h2 style="color:var(--secondary);">Password Reset Successful!</h2>
                <p>Your password has been updated successfully.</p>
            </div>
            <a href="login.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:20px;">
                <i class="fas fa-sign-in-alt"></i> Login Now
            </a>
        <?php else: ?>
            <div class="auth-header">
                <div class="auth-icon">🔒</div>
                <h2>Set New Password</h2>
                <p>Create a new password for <strong style="color:var(--primary-light);"><?= sanitize($email) ?></strong></p>
            </div>
            
            <?php if($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                </div>
                <button type="submit" class="btn btn-secondary" style="width:100%;">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </form>
        <?php endif; ?>
        
        <p class="text-center" style="font-size:0.85rem;margin-top:16px;">
            <a href="login.php" style="color:var(--text-muted);">← Back to Login</a>
        </p>
    </div>
</div>

</body>
</html>
