<?php
require_once '../includes/config.php';
if(!isLoggedIn()) redirect('../login.php');

$complaint = null;
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['id'])) {
    $cid = sanitize($_POST['complaint_id'] ?? $_GET['id'] ?? '');
    if($cid) {
        $stmt = $pdo->prepare("SELECT * FROM complaints WHERE complaint_id = ? AND user_id = ?");
        $stmt->execute([$cid, $_SESSION['user_id']]);
        $complaint = $stmt->fetch();
        if(!$complaint) $error = 'Complaint not found or does not belong to you!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Complaint - Neer Nigrani</title>
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
        <a href="my_complaints.php" class="nav-item"><i class="fas fa-list"></i> My Complaints</a>
        <a href="track_complaint.php" class="nav-item active"><i class="fas fa-search"></i> Track Complaint</a>
        <a href="../index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn btn-danger btn-sm" style="width:100%;text-align:center;">Logout</a>
        </div>
    </aside>
    
    <main class="main-content">
        <button class="hamburger" onclick="toggleSidebar()" style="display:none;margin-bottom:16px;">☰ Menu</button>
        <div class="page-header">
            <h1>🔍 Track Complaint</h1>
            <p>Complaint ID se status track karein</p>
        </div>
        
        <div class="card fade-in" style="max-width:500px;">
            <form method="POST">
                <div class="form-group">
                    <label>Complaint ID</label>
                    <input type="text" name="complaint_id" class="form-control" placeholder="e.g. NN-20260326-ABCDE" required value="<?= sanitize($cid ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">🔍 Track Status</button>
            </form>
        </div>
        
        <?php if($error): ?><div class="msg-error mt-2"><?= $error ?></div><?php endif; ?>
        
        <?php if($complaint): ?>
        <div class="card mt-4 fade-in" style="max-width:600px;">
            <div class="flex-between mb-2">
                <span class="complaint-id" style="font-size:1rem;"><?= sanitize($complaint['complaint_id']) ?></span>
                <span class="badge badge-<?= $complaint['status']=='Pending'?'pending':($complaint['status']=='In Progress'?'progress':'resolved') ?>" style="font-size:0.9rem;padding:6px 16px;">
                    ● <?= $complaint['status'] ?>
                </span>
            </div>
            <h3><?= sanitize($complaint['issue_type']) ?></h3>
            <p style="color:var(--text-muted);margin:8px 0;"><?= sanitize($complaint['description']) ?></p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;font-size:0.85rem;">
                <div><strong>📍 Area:</strong> <?= sanitize($complaint['area']) ?></div>
                <div><strong>🏛 District:</strong> <?= sanitize($complaint['district']) ?></div>
                <div><strong>📅 Filed:</strong> <?= date('d M Y, h:i A', strtotime($complaint['created_at'])) ?></div>
                <div><strong>🔄 Updated:</strong> <?= date('d M Y, h:i A', strtotime($complaint['updated_at'])) ?></div>
            </div>
            <?php if($complaint['admin_remarks']): ?>
                <div class="alert-box info mt-2">
                    <span>💬</span>
                    <div>
                        <div class="alert-title">Admin Remarks</div>
                        <div class="alert-msg"><?= sanitize($complaint['admin_remarks']) ?></div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($complaint['photo']): ?>
                <div class="mt-2">
                    <strong>📷 Photo:</strong><br>
                    <img src="../uploads/<?= sanitize($complaint['photo']) ?>" style="max-width:300px;border-radius:8px;margin-top:8px;">
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>
</div>
<script src="../js/script.js"></script>
<script>if(window.innerWidth<=768)document.querySelector('.hamburger').style.display='block';</script>
</body>
</html>
