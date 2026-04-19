<?php require_once 'includes/config.php'; 

// Fetch active alerts
$alerts = $pdo->query("SELECT * FROM alerts WHERE is_active = 1 ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalComplaints = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$resolvedComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status='Resolved'")->fetchColumn();
$pendingComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status='Pending'")->fetchColumn();

// Water supply
$supplies = $pdo->query("SELECT * FROM water_supply WHERE status='Active' ORDER BY district")->fetchAll();
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neer Nigrani - जल शक्ति Water Monitoring System</title>
    <meta name="description" content="Bihar Jal Shakti water monitoring, complaint management and supply tracking system">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="wave-container">
    <div class="wave"></div>
    <div class="wave"></div>
</div>

<!-- Navbar -->
<nav class="navbar">
    <a href="index.php" class="logo">
        <img src="images/logo.jpeg" alt="Neer Nigrani Logo">
        <span>💧 Neer Nigrani</span>
    </a>
    <button class="hamburger" onclick="toggleNav()">☰</button>
    <ul class="nav-links">
        <li><a href="#dashboard">📊 Dashboard</a></li>
        <li><a href="#water-supply">🕐 Water Timing</a></li>
        <li><a href="#features">⚡ Features</a></li>
        <li><span id="locationBadge" class="location-badge">📍 Detecting...</span></li>
        <?php if(isLoggedIn()): ?>
            <li><a href="pages/user_dashboard.php" class="btn btn-secondary btn-sm">My Dashboard</a></li>
            <li><a href="logout.php" class="btn btn-outline btn-sm">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="btn btn-primary btn-sm">Login</a></li>
            <li><a href="register.php" class="btn btn-outline btn-sm">Register</a></li>
        <?php endif; ?>
        <li><a href="admin/login.php" class="btn btn-accent btn-sm">🛡 Admin</a></li>
    </ul>
</nav>

<!-- Alerts Banner -->
<?php foreach($alerts as $alert): ?>
<div class="alert-box <?= strtolower($alert['alert_type']) ?> fade-in" style="max-width:1200px;margin:16px auto;">
    <span class="alert-icon">
        <?= $alert['alert_type'] == 'Emergency' ? '🚨' : ($alert['alert_type'] == 'Warning' ? '⚠️' : 'ℹ️') ?>
    </span>
    <div style="flex:1">
        <div class="alert-title"><?= sanitize($alert['title']) ?></div>
        <div class="alert-msg"><?= sanitize($alert['message']) ?> — <strong><?= sanitize($alert['district']) ?></strong></div>
    </div>
    <button onclick="closeAlert(this)" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1.2rem;">✕</button>
</div>
<?php endforeach; ?>

<!-- Hero -->
<section class="hero fade-in">
    <h1>🌊 <span class="gradient-text">Neer Nigrani</span></h1>
    <h1>जल शक्ति Monitoring System</h1>
    <p>Bihar ke har jile me paani ki gunwatta, supply timing aur complaint management — ek hi platform par.</p>
    <div class="flex gap-2" style="justify-content:center;flex-wrap:wrap;">
        <?php if(isLoggedIn()): ?>
            <a href="pages/new_complaint.php" class="btn btn-primary btn-lg">📝 File Complaint</a>
            <a href="pages/user_dashboard.php" class="btn btn-secondary btn-lg">📊 My Dashboard</a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary btn-lg">🚀 Get Started</a>
            <a href="login.php" class="btn btn-outline btn-lg">🔑 Login</a>
        <?php endif; ?>
    </div>
</section>

<!-- Dashboard Stats -->
<section id="dashboard" class="section">
    <h2 class="section-title">📊 Live Dashboard</h2>
    <p class="section-subtitle">Real-time water management statistics across Bihar</p>
    
    <div class="stats-grid">
        <div class="card stat-card blue fade-in">
            <div class="stat-icon"><i class="fas fa-tint"></i></div>
            <div class="stat-value" data-count="12450">12,450</div>
            <div class="stat-label">कुल जल स्रोत</div>
        </div>
        <div class="card stat-card green fade-in">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?= number_format($totalUsers) ?></div>
            <div class="stat-label">Registered Users</div>
        </div>
        <div class="card stat-card blue fade-in">
            <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-value"><?= number_format($totalComplaints) ?></div>
            <div class="stat-label">Total Complaints</div>
        </div>
        <div class="card stat-card green fade-in">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value"><?= number_format($resolvedComplaints) ?></div>
            <div class="stat-label">Resolved</div>
        </div>
        <div class="card stat-card orange fade-in">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-value"><?= number_format($pendingComplaints) ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="card stat-card red fade-in">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-value"><?= count($alerts) ?></div>
            <div class="stat-label">Active Alerts</div>
        </div>
    </div>
</section>

<!-- Water Supply Timing -->
<section id="water-supply" class="section">
    <h2 class="section-title">🕐 Water Supply Timing</h2>
    <p class="section-subtitle">Bihar ke districts ka water supply schedule</p>
    
    <div class="table-wrapper card">
        <table>
            <thead>
                <tr>
                    <th>District</th>
                    <th>Area</th>
                    <th>🌅 Morning</th>
                    <th>🌆 Evening</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($supplies as $s): ?>
                <tr>
                    <td><strong><?= sanitize($s['district']) ?></strong></td>
                    <td><?= sanitize($s['area']) ?></td>
                    <td><?= sanitize($s['morning_time']) ?></td>
                    <td><?= sanitize($s['evening_time']) ?></td>
                    <td><span class="badge badge-resolved">● Active</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Features -->
<section id="features" class="section">
    <h2 class="section-title">⚡ Features</h2>
    <p class="section-subtitle">Explore what Neer Nigrani offers</p>
    
    <div class="grid-3">
        <div class="card feature-card fade-in">
            <div class="feature-icon" style="background:rgba(0,119,182,0.15);color:var(--primary-light);">📝</div>
            <h3>Complaint Filing</h3>
            <p>Apni area ki paani ki samasya seedha yahan report karein</p>
        </div>
        <div class="card feature-card fade-in">
            <div class="feature-icon" style="background:rgba(6,214,160,0.15);color:var(--secondary);">🔍</div>
            <h3>Track Complaint</h3>
            <p>Unique ID se apni complaint ka status real-time track karein</p>
        </div>
        <div class="card feature-card fade-in">
            <div class="feature-icon" style="background:rgba(247,127,0,0.15);color:var(--accent);">🚨</div>
            <h3>Water Alerts</h3>
            <p>Emergency water alerts aur notifications turant paayein</p>
        </div>
        <div class="card feature-card fade-in">
            <div class="feature-icon" style="background:rgba(0,180,216,0.15);color:var(--primary-light);">📍</div>
            <h3>Auto Location</h3>
            <p>GPS se automatically aapka area detect hota hai</p>
        </div>
        <div class="card feature-card fade-in">
            <div class="feature-icon" style="background:rgba(255,209,102,0.15);color:var(--warning);">📊</div>
            <h3>Live Dashboard</h3>
            <p>Real-time statistics aur water quality monitoring</p>
        </div>
        <div class="card feature-card fade-in">
            <div class="feature-icon" style="background:rgba(239,71,111,0.15);color:var(--danger);">🛡</div>
            <h3>Admin Panel</h3>
            <p>Admin complaints manage kare aur alerts publish kare</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <p>💧 Neer Nigrani — जल शक्ति Water Monitoring System © <?= date('Y') ?> | Bihar Government Initiative</p>
</footer>

<script src="js/script.js"></script>
</body>
</html>
