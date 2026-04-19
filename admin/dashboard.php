<?php
require_once '../includes/config.php';
if(!isAdmin()) redirect('login.php');

// Handle status update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if($_POST['action'] === 'update_status') {
        $stmt = $pdo->prepare("UPDATE complaints SET status = ?, admin_remarks = ? WHERE id = ?");
        $stmt->execute([sanitize($_POST['status']), sanitize($_POST['remarks'] ?? ''), intval($_POST['complaint_id'])]);
    }
    if($_POST['action'] === 'publish_alert') {
        $stmt = $pdo->prepare("INSERT INTO alerts (title, message, district, alert_type, created_by) VALUES (?,?,?,?,?)");
        $stmt->execute([sanitize($_POST['title']), sanitize($_POST['message']), sanitize($_POST['district']), sanitize($_POST['alert_type']), $_SESSION['admin_id']]);
    }
    if($_POST['action'] === 'delete_alert') {
        $stmt = $pdo->prepare("UPDATE alerts SET is_active = 0 WHERE id = ?");
        $stmt->execute([intval($_POST['alert_id'])]);
    }
    redirect('dashboard.php');
}

// Fetch data
$complaints = $pdo->query("SELECT c.*, u.name as user_name, u.email as user_email, u.mobile as user_mobile FROM complaints c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC")->fetchAll();
$alerts = $pdo->query("SELECT * FROM alerts WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

$total = count($complaints);
$pending = count(array_filter($complaints, fn($c) => $c['status'] === 'Pending'));
$inProgress = count(array_filter($complaints, fn($c) => $c['status'] === 'In Progress'));
$resolved = count(array_filter($complaints, fn($c) => $c['status'] === 'Resolved'));

$districts = ['Patna','Gaya','Muzaffarpur','Bhagalpur','Darbhanga','Purnia','Nalanda','Vaishali','Samastipur','Begusarai','Munger','Saran','Siwan','Gopalganj','Madhubani','Sitamarhi','Araria','Kishanganj','Katihar','Saharsa'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Neer Nigrani</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="../index.php" class="logo"><img src="../images/logo.jpeg" alt="Logo"><span>Admin Panel</span></a>
        </div>
        <a href="#stats" class="nav-item active"><i class="fas fa-chart-bar"></i> Overview</a>
        <a href="#complaints" class="nav-item"><i class="fas fa-clipboard-list"></i> Complaints</a>
        <a href="#alerts" class="nav-item"><i class="fas fa-bell"></i> Alerts</a>
        <a href="#users" class="nav-item"><i class="fas fa-users"></i> Users</a>
        <a href="../index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
        <div class="sidebar-footer">
            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:8px;">
                🛡 <?= sanitize($_SESSION['admin_name']) ?>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm" style="width:100%;text-align:center;">Logout</a>
        </div>
    </aside>
    
    <main class="main-content">
        <button class="hamburger" onclick="toggleSidebar()" style="display:none;margin-bottom:16px;">☰ Menu</button>
        
        <div class="page-header">
            <h1>🛡 Admin Dashboard</h1>
            <p>Manage complaints, alerts & users</p>
        </div>
        
        <!-- Stats -->
        <div id="stats" class="grid-4 mb-4">
            <div class="card stat-card blue"><div class="stat-value"><?= $total ?></div><div class="stat-label">Total Complaints</div></div>
            <div class="card stat-card orange"><div class="stat-value"><?= $pending ?></div><div class="stat-label">Pending</div></div>
            <div class="card stat-card blue"><div class="stat-value"><?= $inProgress ?></div><div class="stat-label">In Progress</div></div>
            <div class="card stat-card green"><div class="stat-value"><?= $resolved ?></div><div class="stat-label">Resolved</div></div>
        </div>
        
        <!-- Complaints -->
        <div id="complaints" class="mb-4">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;margin-bottom:12px;">
                <h2 class="section-title" style="font-size:1.3rem;margin-bottom:0;">📋 All Complaints</h2>
                <button class="btn btn-primary btn-sm" onclick="generateAdminReport()" style="white-space:nowrap;">
                    <i class="fas fa-file-alt"></i> Generate Report
                </button>
            </div>
            
            <?php if(empty($complaints)): ?>
                <div class="card text-center" style="padding:30px;">No complaints yet.</div>
            <?php else: ?>
                <?php foreach($complaints as $c): ?>
                <div class="card mb-2 fade-in">
                    <div class="flex-between flex-wrap gap-2">
                        <div>
                            <span class="complaint-id"><?= sanitize($c['complaint_id']) ?></span>
                            <span class="badge badge-<?= $c['status']=='Pending'?'pending':($c['status']=='In Progress'?'progress':'resolved') ?>" style="margin-left:8px;">● <?= $c['status'] ?></span>
                            <h4 style="margin-top:8px;"><?= sanitize($c['issue_type']) ?></h4>
                            <p style="font-size:0.85rem;color:var(--text-muted);"><?= sanitize($c['description']) ?></p>
                            <div class="complaint-meta" style="margin-top:8px;">
                                <span>👤 <?= sanitize($c['user_name']) ?> (<?= sanitize($c['user_email']) ?>)</span>
                                <span>📱 <?= sanitize($c['user_mobile']) ?></span>
                                <span>📍 <?= sanitize($c['area']) ?>, <?= sanitize($c['district']) ?></span>
                                <span>📅 <?= date('d M Y', strtotime($c['created_at'])) ?></span>
                            </div>
                            <?php if($c['photo']): ?>
                                <img src="../uploads/<?= sanitize($c['photo']) ?>" style="max-width:150px;border-radius:8px;margin-top:8px;">
                            <?php endif; ?>
                        </div>
                        <div style="min-width:200px;">
                            <form method="POST" style="display:flex;flex-direction:column;gap:8px;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                                <select name="status" class="form-control" style="padding:8px;">
                                    <option value="Pending" <?= $c['status']=='Pending'?'selected':'' ?>>Pending</option>
                                    <option value="In Progress" <?= $c['status']=='In Progress'?'selected':'' ?>>In Progress</option>
                                    <option value="Resolved" <?= $c['status']=='Resolved'?'selected':'' ?>>Resolved</option>
                                </select>
                                <input type="text" name="remarks" class="form-control" placeholder="Admin remarks..." style="padding:8px;" value="<?= sanitize($c['admin_remarks'] ?? '') ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Publish Alert -->
        <div id="alerts" class="mb-4">
            <h2 class="section-title" style="font-size:1.3rem;">🚨 Publish Water Alert</h2>
            <div class="card fade-in" style="max-width:600px;">
                <form method="POST">
                    <input type="hidden" name="action" value="publish_alert">
                    <div class="form-group">
                        <label>Alert Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Water Supply Disrupted" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" placeholder="Alert details..." required></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label>District</label>
                            <select name="district" class="form-control" required>
                                <?php foreach($districts as $d): ?><option value="<?= $d ?>"><?= $d ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="alert_type" class="form-control">
                                <option value="Emergency">🚨 Emergency</option>
                                <option value="Warning">⚠️ Warning</option>
                                <option value="Info">ℹ️ Info</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width:100%;">🚨 Publish Alert</button>
                </form>
            </div>
            
            <h3 class="mt-4" style="font-size:1.1rem;">Active Alerts</h3>
            <?php foreach($alerts as $a): ?>
            <div class="alert-box <?= strtolower($a['alert_type']) ?> mb-2">
                <span class="alert-icon"><?= $a['alert_type']=='Emergency'?'🚨':'⚠️' ?></span>
                <div style="flex:1">
                    <div class="alert-title"><?= sanitize($a['title']) ?></div>
                    <div class="alert-msg"><?= sanitize($a['message']) ?> — <?= sanitize($a['district']) ?></div>
                </div>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete_alert">
                    <input type="hidden" name="alert_id" value="<?= $a['id'] ?>">
                    <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Remove this alert?')">✕</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Users -->
        <div id="users">
            <h2 class="section-title" style="font-size:1.3rem;">👥 Registered Users (<?= count($users) ?>)</h2>
            <div class="table-wrapper card">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Mobile</th><th>Registered</th></tr></thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= sanitize($u['name']) ?></td>
                            <td><?= sanitize($u['email']) ?></td>
                            <td><?= sanitize($u['mobile']) ?></td>
                            <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script src="../js/report.js"></script>
<script src="../js/script.js"></script>
<script>if(window.innerWidth<=768)document.querySelector('.hamburger').style.display='block';</script>
</body>
</html>
