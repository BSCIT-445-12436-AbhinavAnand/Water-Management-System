<?php
require_once 'includes/config.php';
if(isLoggedIn()) redirect('pages/user_dashboard.php');

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        redirect('pages/user_dashboard.php');
    } else {
        $error = 'Invalid email or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Neer Nigrani</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="wave-container"><div class="wave"></div><div class="wave"></div></div>

<div class="auth-container">
    <div class="card auth-card fade-in">
        <div class="auth-header">
            <div class="auth-icon">🔑</div>
            <h2>Welcome Back!</h2>
            <p>Sign in to your Neer Nigrani account</p>
        </div>
        
        <?php if($error): ?>
            <div class="msg-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" required value="<?= sanitize($email ?? '') ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
        </form>
        
        <p class="text-center" style="font-size:0.85rem;margin-top:12px;">
            <a href="forgot_password.php" style="color:var(--accent);font-weight:500;"><i class="fas fa-key"></i> Forgot Password?</a>
        </p>
        
        <p class="text-center mt-2" style="font-size:0.9rem;color:var(--text-muted);">
            Don't have an account? <a href="register.php" style="color:var(--primary-light);">Register</a>
        </p>
        <p class="text-center" style="font-size:0.85rem;margin-top:8px;">
            <a href="index.php" style="color:var(--text-muted);">← Back to Home</a>
        </p>
    </div>
</div>

</body>
</html>
