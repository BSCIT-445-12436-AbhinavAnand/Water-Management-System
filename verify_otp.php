<?php
require_once 'includes/config.php';
if(isLoggedIn()) redirect('pages/user_dashboard.php');
if(!isset($_SESSION['reset_email'])) redirect('forgot_password.php');

$email = $_SESSION['reset_email'];
$error = '';
$attempts_key = 'otp_attempts_' . md5($email);

if(!isset($_SESSION[$attempts_key])) $_SESSION[$attempts_key] = 0;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = sanitize($_POST['otp'] ?? '');
    
    // Max 5 attempts
    if($_SESSION[$attempts_key] >= 5) {
        $error = 'Too many wrong attempts! Please request a new OTP.';
    } else {
        // Verify OTP
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND otp = ? AND is_used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$email, $otp]);
        $record = $stmt->fetch();
        
        if($record) {
            // OTP is valid
            $_SESSION['otp_verified'] = true;
            $_SESSION[$attempts_key] = 0;
            redirect('reset_password.php');
        } else {
            $_SESSION[$attempts_key]++;
            $remaining = 5 - $_SESSION[$attempts_key];
            
            // Check if OTP expired
            $stmt2 = $pdo->prepare("SELECT * FROM password_resets WHERE email = ? AND otp = ? AND is_used = 0 ORDER BY id DESC LIMIT 1");
            $stmt2->execute([$email, $otp]);
            $expired = $stmt2->fetch();
            
            if($expired) {
                $error = 'OTP has expired! Please request a new one.';
            } else {
                $error = "Invalid OTP! $remaining attempt(s) remaining.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Neer Nigrani</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .otp-inputs { display:flex; gap:10px; justify-content:center; margin:20px 0; }
        .otp-inputs input {
            width:50px; height:55px; text-align:center; font-size:1.5rem; font-weight:700;
            background:rgba(255,255,255,0.06); border:2px solid rgba(0,180,216,0.3);
            border-radius:12px; color:#06d6a0; font-family:inherit;
            transition:all 0.3s;
        }
        .otp-inputs input:focus { border-color:#00b4d8; box-shadow:0 0 0 3px rgba(0,180,216,0.2); outline:none; background:rgba(255,255,255,0.1); }
        .timer { text-align:center; color:var(--text-muted); font-size:0.85rem; margin-bottom:16px; }
        .timer span { color:#ffd166; font-weight:600; }
    </style>
</head>
<body>

<div class="wave-container"><div class="wave"></div><div class="wave"></div></div>

<div class="auth-container">
    <div class="card auth-card fade-in">
        <div class="auth-header">
            <div class="auth-icon">📩</div>
            <h2>Verify OTP</h2>
            <p>Enter the 6-digit OTP sent to <strong style="color:var(--primary-light);"><?= sanitize($email) ?></strong></p>
        </div>
        
        <?php if($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
        
        <?php if(isset($_SESSION['debug_otp'])): ?>
            <div class="msg-success" style="text-align:center;">
                <strong>Testing Mode:</strong> Your OTP is <span style="font-size:1.3rem;letter-spacing:3px;color:#06d6a0;"><?= $_SESSION['debug_otp'] ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-box" autofocus>
                <input type="text" maxlength="1" class="otp-box">
                <input type="text" maxlength="1" class="otp-box">
                <input type="text" maxlength="1" class="otp-box">
                <input type="text" maxlength="1" class="otp-box">
                <input type="text" maxlength="1" class="otp-box">
            </div>
            <input type="hidden" name="otp" id="otpHidden">
            
            <div class="timer">OTP expires in <span id="countdown">10:00</span></div>
            
            <button type="submit" class="btn btn-secondary" style="width:100%;" id="verifyBtn">
                <i class="fas fa-check-circle"></i> Verify OTP
            </button>
        </form>
        
        <p class="text-center mt-2" style="font-size:0.85rem;color:var(--text-muted);margin-top:16px;">
            Didn't receive OTP? <a href="forgot_password.php" style="color:var(--primary-light);">Resend OTP</a>
        </p>
        <p class="text-center" style="font-size:0.85rem;margin-top:8px;">
            <a href="login.php" style="color:var(--text-muted);">← Back to Login</a>
        </p>
    </div>
</div>

<script>
// OTP Input handling
const boxes = document.querySelectorAll('.otp-box');
const hidden = document.getElementById('otpHidden');

boxes.forEach((box, i) => {
    box.addEventListener('input', (e) => {
        if(e.target.value && i < boxes.length - 1) boxes[i+1].focus();
        updateHidden();
    });
    box.addEventListener('keydown', (e) => {
        if(e.key === 'Backspace' && !e.target.value && i > 0) boxes[i-1].focus();
    });
    box.addEventListener('paste', (e) => {
        e.preventDefault();
        const data = e.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);
        data.split('').forEach((c,j) => { if(boxes[j]) boxes[j].value = c; });
        updateHidden();
        if(data.length > 0) boxes[Math.min(data.length, boxes.length)-1].focus();
    });
});

function updateHidden() {
    hidden.value = Array.from(boxes).map(b => b.value).join('');
}

// Countdown Timer (10 minutes)
let time = 600;
const cd = document.getElementById('countdown');
const timer = setInterval(() => {
    time--;
    const m = Math.floor(time/60), s = time%60;
    cd.textContent = `${m}:${s.toString().padStart(2,'0')}`;
    if(time <= 60) cd.style.color = '#ef476f';
    if(time <= 0) { clearInterval(timer); cd.textContent = 'EXPIRED'; document.getElementById('verifyBtn').disabled = true; }
}, 1000);
</script>

</body>
</html>
