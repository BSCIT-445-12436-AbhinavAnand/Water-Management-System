<?php
require_once __DIR__ . '/includes/config.php';

$defaultName = 'Super Admin';
$defaultEmail = 'admin@neernigrani.com';
$defaultPassword = 'Admin@123';
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
$stmt->execute([$defaultEmail]);
$existingAdmin = $stmt->fetch();

if ($existingAdmin) {
    $update = $pdo->prepare('UPDATE admins SET name = ?, password = ? WHERE id = ?');
    $update->execute([$defaultName, $hashedPassword, $existingAdmin['id']]);
    $message = 'Admin account updated successfully.';
} else {
    $insert = $pdo->prepare('INSERT INTO admins (name, email, password) VALUES (?, ?, ?)');
    $insert->execute([$defaultName, $defaultEmail, $hashedPassword]);
    $message = 'Admin account created successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Setup - Neer Nigrani</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="auth-container">
    <div class="card auth-card">
      <h2>✅ Admin Setup Complete</h2>
      <p><?= htmlspecialchars($message) ?></p>
      <p><strong>Email:</strong> admin@neernigrani.com</p>
      <p><strong>Password:</strong> Admin@123</p>
      <p style="margin-top:12px;color:#d9534f;"><strong>Security:</strong> Please delete <code>setup_admin.php</code> after successful login.</p>
      <a href="admin/login.php" class="btn btn-accent" style="display:inline-block;margin-top:12px;">Go to Admin Login</a>
    </div>
  </div>
</body>
</html>
