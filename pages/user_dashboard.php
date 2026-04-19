<?php
require_once '../includes/config.php';
if(!isLoggedIn()) redirect('../login.php');

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Fetch user complaints
$stmt = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$complaints = $stmt->fetchAll();

// Fetch active alerts
$alerts = $pdo->query("SELECT * FROM alerts WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Stats
$total = count($complaints);
$pending = count(array_filter($complaints, fn($c) => $c['status'] === 'Pending'));
$inProgress = count(array_filter($complaints, fn($c) => $c['status'] === 'In Progress'));
$resolved = count(array_filter($complaints, fn($c) => $c['status'] === 'Resolved'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Neer Nigrani</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="../index.php" class="logo">
                <img src="../images/logo.jpeg" alt="Logo">
                <span>Neer Nigrani</span>
            </a>
        </div>
        <a href="user_dashboard.php" class="nav-item active"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="new_complaint.php" class="nav-item"><i class="fas fa-plus-circle"></i> New Complaint</a>
        <a href="my_complaints.php" class="nav-item"><i class="fas fa-list"></i> My Complaints</a>
        <a href="track_complaint.php" class="nav-item"><i class="fas fa-search"></i> Track Complaint</a>
        <a href="../index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
        <div class="sidebar-footer">
            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:8px;">
                <i class="fas fa-user"></i> <?= sanitize($userName) ?>
            </div>
            <a href="../logout.php" class="btn btn-danger btn-sm" style="width:100%;text-align:center;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>
    
    <!-- Main -->
    <main class="main-content">
        <button class="hamburger" onclick="toggleSidebar()" style="display:none;margin-bottom:16px;">☰ Menu</button>
        
        <div class="page-header">
            <h1>👋 Namaste, <?= sanitize($userName) ?>!</h1>
            <p>Aapka complaint dashboard</p>
        </div>
        
        <!-- Alerts -->
        <?php foreach($alerts as $a): ?>
        <div class="alert-box <?= strtolower($a['alert_type']) ?> fade-in">
            <span class="alert-icon"><?= $a['alert_type']=='Emergency' ? '🚨' : '⚠️' ?></span>
            <div>
                <div class="alert-title"><?= sanitize($a['title']) ?></div>
                <div class="alert-msg"><?= sanitize($a['message']) ?> — <?= sanitize($a['district']) ?></div>
            </div>
            <button onclick="closeAlert(this)" style="background:none;border:none;color:var(--text-muted);cursor:pointer;">✕</button>
        </div>
        <?php endforeach; ?>
        
        <!-- Stats -->
        <div class="grid-4 mb-4">
            <div class="card stat-card blue fade-in">
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-label">Total Complaints</div>
            </div>
            <div class="card stat-card orange fade-in">
                <div class="stat-value"><?= $pending ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="card stat-card blue fade-in">
                <div class="stat-value"><?= $inProgress ?></div>
                <div class="stat-label">In Progress</div>
            </div>
            <div class="card stat-card green fade-in">
                <div class="stat-value"><?= $resolved ?></div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>
        
        <!-- Quick Action -->
        <div class="flex gap-2 mb-4 flex-wrap">
            <a href="new_complaint.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Complaint</a>
            <a href="track_complaint.php" class="btn btn-outline"><i class="fas fa-search"></i> Track Complaint</a>
        </div>
        
        <!-- Recent Complaints -->
        <h2 class="section-title" style="font-size:1.3rem;">📋 Recent Complaints</h2>
        
        <?php if(empty($complaints)): ?>
            <div class="card text-center" style="padding:40px;">
                <p style="font-size:3rem;margin-bottom:12px;">📭</p>
                <h3>Koi complaint nahi hai</h3>
                <p style="color:var(--text-muted);">Abhi tak aapne koi complaint file nahi ki.</p>
                <a href="new_complaint.php" class="btn btn-primary mt-2">📝 File First Complaint</a>
            </div>
        <?php else: ?>
            <?php foreach(array_slice($complaints, 0, 5) as $c): ?>
            <div class="card complaint-card mb-2 fade-in">
                <div>
                    <span class="complaint-id"><?= sanitize($c['complaint_id']) ?></span>
                    <h4><?= sanitize($c['issue_type']) ?></h4>
                    <p style="font-size:0.85rem;color:var(--text-muted);"><?= sanitize(substr($c['description'],0,100)) ?>...</p>
                    <div class="complaint-meta">
                        <span>📍 <?= sanitize($c['area']) ?>, <?= sanitize($c['district']) ?></span>
                        <span>📅 <?= date('d M Y', strtotime($c['created_at'])) ?></span>
                    </div>
                </div>
                <div>
                    <span class="badge badge-<?= $c['status']=='Pending'?'pending':($c['status']=='In Progress'?'progress':'resolved') ?>">
                        ● <?= $c['status'] ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if($total > 5): ?>
                <a href="my_complaints.php" class="btn btn-outline mt-2">View All →</a>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<script src="../js/script.js"></script>
<script>
if(window.innerWidth <= 768) document.querySelector('.hamburger').style.display = 'block';
</script>
</body>
</html>
