<?php
require_once '../includes/config.php';
if(!isLoggedIn()) redirect('../login.php');

$stmt = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$complaints = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - Neer Nigrani</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="../index.php" class="logo"><img src="../images/logo.jpeg" alt="Logo"><span>Neer Nigrani</span></a>
        </div>
        <a href="user_dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="new_complaint.php" class="nav-item"><i class="fas fa-plus-circle"></i> New Complaint</a>
        <a href="my_complaints.php" class="nav-item active"><i class="fas fa-list"></i> My Complaints</a>
        <a href="track_complaint.php" class="nav-item"><i class="fas fa-search"></i> Track Complaint</a>
        <a href="../index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn btn-danger btn-sm" style="width:100%;text-align:center;">Logout</a>
        </div>
    </aside>
    
    <main class="main-content">
        <button class="hamburger" onclick="toggleSidebar()" style="display:none;margin-bottom:16px;">☰ Menu</button>
        <div class="page-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;">
            <div>
                <h1>📋 My Complaints</h1>
                <p>Aapki sabhi complaints ki list</p>
            </div>
            <button class="btn btn-primary" onclick="generateReport('table', 'My Complaints Report - Neer Nigrani')" style="white-space:nowrap;">
                <i class="fas fa-file-alt"></i> Generate Report
            </button>
        </div>
        <div style="margin-bottom:16px;">
            <button class="btn btn-primary" onclick="generateReport('table', 'My Complaints Report - Neer Nigrani')"><i class="fas fa-file-alt"></i> 📄 Generate Report</button>

        </div>
        
        <?php if(empty($complaints)): ?>
            <div class="card text-center" style="padding:40px;">
                <p style="font-size:3rem;">📭</p>
                <h3>No complaints yet</h3>
                <a href="new_complaint.php" class="btn btn-primary mt-2">📝 File Complaint</a>
            </div>
        <?php else: ?>
            <div class="table-wrapper card">
                <table>
                    <thead>
                        <tr><th>Complaint ID</th><th>Issue</th><th>Area</th><th>District</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($complaints as $c): ?>
                        <tr>
                            <td><span class="complaint-id" style="display:inline;"><?= sanitize($c['complaint_id']) ?></span></td>
                            <td><?= sanitize($c['issue_type']) ?></td>
                            <td><?= sanitize($c['area']) ?></td>
                            <td><?= sanitize($c['district']) ?></td>
                            <td><span class="badge badge-<?= $c['status']=='Pending'?'pending':($c['status']=='In Progress'?'progress':'resolved') ?>">● <?= $c['status'] ?></span></td>
                            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>
<script src="../js/report.js"></script>
<script src="../js/script.js"></script>
<script>if(window.innerWidth<=768)document.querySelector('.hamburger').style.display='block';</script>
</body>
</html>
