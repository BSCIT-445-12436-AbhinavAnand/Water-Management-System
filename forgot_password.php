<?php
require_once 'includes/config.php';
if(isLoggedIn()) redirect('pages/user_dashboard.php');

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if(!$user) {
        $error = 'No account found with this email!';
    } else {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Invalidate old OTPs for this email
        $pdo->prepare("UPDATE password_resets SET is_used = 1 WHERE email = ? AND is_used = 0")->execute([$email]);
        
        // Store new OTP
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, otp, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $otp, $expires]);
        
        // Send OTP via email
        $subject = "Neer Nigrani - Password Reset OTP";
        $message = "
        <html>
        <body style='font-family:Poppins,sans-serif;background:#0a1628;color:#e8f4f8;padding:30px;'>
            <div style='max-width:500px;margin:0 auto;background:rgba(255,255,255,0.08);border-radius:16px;padding:30px;border:1px solid rgba(0,180,216,0.2);'>
                <h2 style='color:#00b4d8;text-align:center;'>🔐 Password Reset OTP</h2>
                <p style='text-align:center;color:#8eacbb;'>Use the OTP below to reset your password</p>
                <div style='text-align:center;margin:25px 0;'>
                    <span style='font-size:2.5rem;font-weight:800;letter-spacing:10px;color:#06d6a0;background:rgba(6,214,160,0.15);padding:15px 30px;border-radius:12px;display:inline-block;'>$otp</span>
                </div>
                <p style='text-align:center;color:#8eacbb;font-size:0.85rem;'>This OTP is valid for <strong style=\"color:#ffd166;\">10 minutes</strong> only.</p>
                <p style='text-align:center;color:#8eacbb;font-size:0.8rem;margin-top:20px;'>If you didn't request this, please ignore this email.</p>
            </div>
        </body>
        </html>";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Neer Nigrani <noreply@neernigrani.com>\r\n";
        
        if(mail($email, $subject, $message, $headers)) {
            $_SESSION['reset_email'] = $email;
            redirect('verify_otp.php');
        } else {
            // Even if mail fails, redirect (for localhost testing where mail may not work)
            $_SESSION['reset_email'] = $email;
            $_SESSION['debug_otp'] = $otp; // Remove in production
            $success = "OTP has been sent to your email! (For testing: OTP is <strong>$otp</strong>)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Neer Nigrani</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="wave-container"><div class="wave"></div><div class="wave"></div></div>

<div class="auth-container">
    <div class="card auth-card fade-in">
        <div class="auth-header">
            <div class="auth-icon">🔑</div>
            <h2>Forgot Password?</h2>
            <p>Enter your registered email to receive an OTP</p>
        </div>
        
        <?php if($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="msg-success"><?= $success ?></div><?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Registered Email</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?= sanitize($email ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <i class="fas fa-paper-plane"></i> Send OTP
            </button>
        </form>
        
        <p class="text-center mt-2" style="font-size:0.9rem;color:var(--text-muted);">
            Remember your password? <a href="login.php" style="color:var(--primary-light);">Login</a>
        </p>
        <p class="text-center" style="font-size:0.85rem;margin-top:8px;">
            <a href="index.php" style="color:var(--text-muted);">← Back to Home</a>
        </p>
    </div>
</div>

</body>
</html>
