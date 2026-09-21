<?php
// admin/index.php - Dedicated Administrator Authentication Portal
require_once '../config/db.php';
require_once '../includes/functions.php';

// Quick 1-click bypass for testing
if (isset($_GET['quick']) && $_GET['quick'] === '1') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email']    = $user['email'];
        $_SESSION['role']     = $user['role'];
        set_flash('success', 'Authenticated successfully as Royal Administrator.');
        header("Location: dashboard.php");
        exit;
    }
}

// If already authenticated as admin, redirect straight to dashboard
if (is_admin()) {
    header("Location: dashboard.php");
    exit;
}

$pageTitle = "Admin Sign In";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash('danger', 'Please provide administrator credentials.');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['role'] === 'admin' && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            set_flash('success', 'Authenticated successfully as Administrator.');
            header("Location: dashboard.php");
            exit;
        } else {
            set_flash('danger', 'Invalid administrator credentials. Access denied.');
        }
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sign In | Bhakti Events Royal Console</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body style="background: radial-gradient(circle at 10% 20%, rgba(122, 28, 46, 0.4) 0%, transparent 60%), radial-gradient(circle at 90% 80%, rgba(197, 160, 89, 0.25) 0%, transparent 60%), linear-gradient(135deg, #1A0307 0%, #2A040A 50%, #150205 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:30px 20px; font-family:var(--font-body);">

<div style="max-width:480px; width:100%;">

  <?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" style="margin-bottom:20px; border-radius:12px; border:1px solid var(--gold); background:#FFFDF9; box-shadow:0 8px 25px rgba(0,0,0,0.3);">
      <div style="font-weight:600;"><i class="fa-solid fa-circle-exclamation me-2 text-gold"></i> <?= e($flash['message']) ?></div>
      <button type="button" class="alert-close" onclick="this.parentElement.remove()" style="background:none; border:none; font-size:18px; cursor:pointer;">&times;</button>
    </div>
  <?php endif; ?>

  <div style="background:#FFFFFF; border-radius:24px; border:1.5px solid var(--gold-border); box-shadow:0 25px 65px rgba(0,0,0,0.6), 0 0 0 1px rgba(197,160,89,0.3); padding:42px 36px; position:relative; overflow:hidden;">
    
    <!-- Watermark -->
    <div style="position:absolute; right:-25px; top:-25px; font-size:160px; color:rgba(122,28,46,0.03); pointer-events:none;">
      <i class="fa-solid fa-crown"></i>
    </div>

    <!-- Header -->
    <div style="text-align:center; margin-bottom:28px; position:relative; z-index:2;">
      <div style="width:62px; height:62px; border-radius:16px; background:var(--burgundy-gradient); color:var(--gold-light); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:26px; border:1.5px solid var(--gold); box-shadow:0 8px 25px rgba(122,28,46,0.35);">
        <i class="fa-solid fa-crown"></i>
      </div>
      <span class="badge-pill" style="background:rgba(197,160,89,0.15); color:var(--burgundy); border-color:var(--gold); font-size:11px; margin-bottom:8px;">
        Bhakti Events &bull; Est. 2014 &bull; Gondal, Gujarat
      </span>
      <h2 style="font-family:var(--font-heading); color:var(--burgundy); font-size:26px; font-weight:800; margin:4px 0 6px;">
        Royal Console Sign In
      </h2>
      <p style="color:var(--charcoal-muted); font-size:13.5px; margin:0;">
        Authorized Event Directorship &amp; Management Portal
      </p>
    </div>

    <form action="index.php" method="POST" id="adminLoginForm">
      <!-- Admin Email -->
      <div class="form-group" style="margin-bottom:18px;">
        <label for="adminEmail" style="font-size:13px; font-weight:600; color:var(--charcoal); display:flex; align-items:center; gap:6px; margin-bottom:6px;">
          <i class="fa-solid fa-user-shield" style="color:var(--gold);"></i> Admin Officer Email
        </label>
        <input type="email" id="adminEmail" name="email" class="form-control" value="admin@eventsphere.com" required autofocus style="padding:12px 16px; border:1.5px solid var(--gold-border); border-radius:10px;">
      </div>

      <!-- Admin Password -->
      <div class="form-group" style="margin-bottom:22px;">
        <label for="adminPassword" style="font-size:13px; font-weight:600; color:var(--charcoal); display:flex; align-items:center; gap:6px; margin-bottom:6px;">
          <i class="fa-solid fa-key" style="color:var(--gold);"></i> Confidential Master Password
        </label>
        <div class="password-input-wrap" style="position:relative;">
          <input type="password" id="adminPassword" name="password" class="form-control" value="password123" required style="padding:12px 45px 12px 16px; border:1.5px solid var(--gold-border); border-radius:10px;">
          <button type="button" class="password-toggle-btn" onclick="togglePass()" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94A3B8; cursor:pointer;">
            <i class="fa-regular fa-eye" id="passIcon"></i>
          </button>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15px; border-radius:12px; font-weight:700; box-shadow:0 8px 25px rgba(122,28,46,0.35); display:flex; align-items:center; justify-content:center; gap:8px;">
        <i class="fa-solid fa-right-to-bracket"></i> Authenticate to Dashboard
      </button>

      <!-- 1-Click Instant Bypass for Evaluators -->
      <div style="margin-top:20px; background:linear-gradient(135deg, #FFFDF9 0%, #FAF5EE 100%); border:1.5px solid var(--gold-border); border-radius:12px; padding:14px 16px; text-align:center;">
        <div style="display:flex; align-items:center; justify-content:center; gap:6px; font-size:12px; font-weight:700; color:var(--burgundy); margin-bottom:4px;">
          <i class="fa-solid fa-bolt" style="color:var(--gold);"></i> Fast Academic Evaluation Access
        </div>
        <p style="font-size:11.5px; color:var(--charcoal-muted); margin:0 0 10px;">Click below for instant one-click console access:</p>
        <a href="index.php?quick=1" class="btn btn-secondary" style="width:100%; padding:9px; font-size:13px; font-weight:700; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none;">
          <i class="fa-solid fa-crown" style="color:var(--burgundy);"></i> Instant 1-Click Administrator Access
        </a>
      </div>
    </form>

    <div style="text-align:center; margin-top:22px; padding-top:16px; border-top:1px solid rgba(0,0,0,0.06); font-size:13px;">
      <a href="../index.php" style="color:var(--charcoal-muted); text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-arrow-left"></i> Return to Public Homepage
      </a>
    </div>
  </div>

  <div style="text-align:center; margin-top:20px; color:var(--gold-light); font-size:12px; opacity:0.85;">
    BCA Semester 5 Academic Project &bull; Parmar Ankita &bull; Bhakti Events
  </div>
</div>

<script>
function togglePass() {
  const p = document.getElementById('adminPassword');
  const icon = document.getElementById('passIcon');
  if (p && icon) {
    const isP = p.type === 'password';
    p.type = isP ? 'text' : 'password';
    icon.className = isP ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
  }
}
</script>

</body>
</html>

