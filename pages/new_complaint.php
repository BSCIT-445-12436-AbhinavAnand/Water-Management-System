<?php
require_once '../includes/config.php';
if(!isLoggedIn()) redirect('../login.php');

$error = '';
$success = '';
$complaintId = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area = sanitize($_POST['area'] ?? '');
    $district = sanitize($_POST['district'] ?? '');
    $issueType = sanitize($_POST['issue_type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $complaintId = generateComplaintId();
    $photo = null;
    
    // Handle photo upload
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed) && $_FILES['photo']['size'] <= 5*1024*1024) {
            $filename = $complaintId . '.' . $ext;
            $uploadDir = '../uploads/';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if(move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
                $photo = $filename;
            }
        } else {
            $error = 'Invalid image! Only JPG, PNG, GIF allowed (max 5MB).';
        }
    }
    
    if(!$error) {
        $stmt = $pdo->prepare("INSERT INTO complaints (complaint_id, user_id, area, district, issue_type, description, photo) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$complaintId, $_SESSION['user_id'], $area, $district, $issueType, $description, $photo]);
        $success = "Complaint filed successfully! Your Complaint ID: <strong>$complaintId</strong>";
    }
}

$districts = ['Patna','Gaya','Muzaffarpur','Bhagalpur','Darbhanga','Purnia','Nalanda','Vaishali','Samastipur','Begusarai','Munger','Saran','Siwan','Gopalganj','Madhubani','Sitamarhi','Araria','Kishanganj','Katihar','Saharsa','Supaul','Madhepura','Khagaria','Jamui','Lakhisarai','Sheikhpura','Nawada','Aurangabad','Rohtas','Buxar','Bhojpur','Banka','East Champaran','West Champaran','Sheohar','Jehanabad','Arwal'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Complaint - Neer Nigrani</title>
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
        <a href="new_complaint.php" class="nav-item active"><i class="fas fa-plus-circle"></i> New Complaint</a>
        <a href="my_complaints.php" class="nav-item"><i class="fas fa-list"></i> My Complaints</a>
        <a href="track_complaint.php" class="nav-item"><i class="fas fa-search"></i> Track Complaint</a>
        <a href="../index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn btn-danger btn-sm" style="width:100%;text-align:center;">Logout</a>
        </div>
    </aside>
    
    <main class="main-content">
        <button class="hamburger" onclick="toggleSidebar()" style="display:none;margin-bottom:16px;">☰ Menu</button>
        
        <div class="page-header">
            <h1>📝 New Complaint</h1>
            <p>Apni area ki paani ki samasya report karein</p>
        </div>
        
        <?php if($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?>
            <div class="msg-success"><?= $success ?></div>
            <div class="card text-center" style="padding:30px;">
                <p style="font-size:2rem;">✅</p>
                <h3>Complaint Registered!</h3>
                <p style="color:var(--text-muted);margin:8px 0;">Save your Complaint ID for tracking:</p>
                <p style="font-size:1.3rem;font-weight:700;color:var(--primary-light);font-family:monospace;background:rgba(0,119,182,0.1);display:inline-block;padding:8px 20px;border-radius:8px;"><?= $complaintId ?></p>
                <div class="flex gap-2 mt-2" style="justify-content:center;">
                    <a href="my_complaints.php" class="btn btn-primary">View Complaints</a>
                    <a href="new_complaint.php" class="btn btn-outline">File Another</a>
                </div>
            </div>
        <?php else: ?>
        
        <div class="card fade-in" style="max-width:700px;">
            <form method="POST" enctype="multipart/form-data">
                <div class="flex gap-2" style="display:grid;grid-template-columns:1fr 1fr;">
                    <div class="form-group">
                        <label>📍 Area / Location</label>
                        <input type="text" name="area" id="area" class="form-control" placeholder="e.g. Kankarbagh" required>
                    </div>
                    <div class="form-group">
                        <label>🏛 District</label>
                        <select name="district" id="district" class="form-control" required>
                            <option value="">Select District</option>
                            <?php foreach($districts as $d): ?>
                                <option value="<?= $d ?>"><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>💧 Water Issue Type</label>
                    <select name="issue_type" class="form-control" required>
                        <option value="">Select Issue</option>
                        <option value="No Water">🚱 No Water Supply</option>
                        <option value="Dirty Water">🟤 Dirty / Contaminated Water</option>
                        <option value="Leakage">💦 Pipeline Leakage</option>
                        <option value="Low Pressure">📉 Low Water Pressure</option>
                        <option value="Other">❓ Other Issue</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>📝 Description</label>
                    <textarea name="description" class="form-control" placeholder="Samasya ka detail likhen..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label>📷 Photo (Optional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div id="imagePreview"></div>
                </div>
                
                <div class="flex gap-1">
                    <button type="button" class="btn btn-outline btn-sm" onclick="detectLocation()">📍 Auto Detect Location</button>
                    <span id="locationBadge" style="font-size:0.8rem;color:var(--text-muted);"></span>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg mt-2" style="width:100%;">
                    <i class="fas fa-paper-plane"></i> Submit Complaint
                </button>
            </form>
        </div>
        
        <?php endif; ?>
    </main>
</div>

<script src="../js/script.js"></script>
<script>if(window.innerWidth<=768)document.querySelector('.hamburger').style.display='block';</script>
</body>
</html>
