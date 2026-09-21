<?php
// user/profile.php - User Account Profile & Security Settings
require_once '../config/db.php';
require_once '../includes/functions.php';

require_login('../login.php');

$userId = $_SESSION['user_id'];

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullname = trim($_POST['fullname'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');

        if (!empty($fullname)) {
            $upStmt = $pdo->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
            $upStmt->execute([$fullname, $phone, $userId]);
            $_SESSION['fullname'] = $fullname;
            $_SESSION['phone'] = $phone;
            set_flash('success', 'Your royal profile details have been saved successfully.');
        } else {
            set_flash('danger', 'Full name cannot be left empty.');
        }
        header("Location: profile.php");
        exit;
    }

    if ($action === 'change_password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass     = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($currentPass) || empty($newPass)) {
            set_flash('danger', 'Please enter your current and new password.');
        } elseif (strlen($newPass) < 6) {
            set_flash('danger', 'New password must be at least 6 characters long.');
        } elseif ($newPass !== $confirmPass) {
            set_flash('danger', 'New password and confirmation do not match.');
        } else {
            $uStmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $uStmt->execute([$userId]);
            $currentHash = $uStmt->fetchColumn();

            if (password_verify($currentPass, $currentHash)) {
                $newHash = password_hash($newPass, PASSWORD_BCRYPT);
                $passStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $passStmt->execute([$newHash, $userId]);
                set_flash('success', 'Your security password has been updated securely.');
            } else {
                set_flash('danger', 'The current password you entered is incorrect.');
            }
        }
        header("Location: profile.php");
        exit;
    }
}

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// User Statistics
$bookingCount = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
$bookingCount->execute([$userId]);
$totalBookings = $bookingCount->fetchColumn();

$spendStmt = $pdo->prepare("SELECT SUM(total_amount) FROM bookings WHERE user_id = ? AND booking_status = 'Confirmed'");
$spendStmt->execute([$userId]);
$totalSpent = $spendStmt->fetchColumn() ?: 0;

$pageTitle = "My Account Profile";
include '../includes/header.php';
?>

<!-- Royal Profile Header Banner -->
<section style="background:linear-gradient(135deg, #1E0307 0%, #3D0712 50%, #170205 100%); color:#fff; padding:55px 20px 65px; border-bottom:2px solid var(--gold); position:relative; overflow:hidden;">
  <div style="position:absolute; right:-30px; top:-30px; font-size:220px; color:rgba(197,160,89,0.04); pointer-events:none;">
    <i class="fa-solid fa-crown"></i>
  </div>

  <div class="container" style="position:relative; z-index:2;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px;">
      <div style="display:flex; align-items:center; gap:22px;">
        <div style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg, #FDE68A 0%, #C5A059 100%); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#1E0307; box-shadow:0 8px 25px rgba(197,160,89,0.4); border:2.5px solid #FFFFFF;">
          <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
        </div>
        <div>
          <span class="badge-pill" style="background:rgba(197,160,89,0.22); color:var(--gold-light); border-color:var(--gold); margin-bottom:8px; font-size:11.5px;">
            <i class="fa-solid fa-crown"></i> <?= is_admin() ? 'Royal Console Administrator' : 'Royal Member Patron' ?> &bull; Est. 2014
          </span>
          <h1 style="color:#FFFFFF; font-size:clamp(24px, 3.5vw, 34px); margin:0 0 6px; font-family:var(--font-heading); font-weight:800;">
            <?= e($user['fullname']) ?>
          </h1>
          <div style="display:flex; gap:16px; color:#F5EBE6; font-size:13.5px; flex-wrap:wrap;">
            <span><i class="fa-regular fa-envelope me-1" style="color:var(--gold-light);"></i> <?= e($user['email']) ?></span>
            <span><i class="fa-solid fa-phone me-1" style="color:var(--gold-light);"></i> <?= !empty($user['phone']) ? e($user['phone']) : '+91 98256 61046' ?></span>
            <span><i class="fa-solid fa-location-dot me-1" style="color:var(--gold-light);"></i> Gondal / Gujarat</span>
          </div>
        </div>
      </div>

      <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <?php if (is_admin()): ?>
          <a href="../admin/dashboard.php" class="btn btn-secondary" style="font-size:13.5px; padding:10px 18px;">
            <i class="fa-solid fa-crown"></i> Open Royal Console
          </a>
        <?php endif; ?>
        <a href="my-bookings.php" class="btn btn-primary" style="font-size:13.5px; padding:10px 18px;">
          <i class="fa-solid fa-ticket"></i> View Royal Passes (<?= $totalBookings ?>)
        </a>
      </div>
    </div>
  </div>
</section>

<div class="container" style="padding:40px 20px 80px;">
  
  <!-- Royal Metrics Strip -->
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:35px;">
    
    <div style="background:#FFFFFF; border-radius:14px; border:1.5px solid var(--gold-border); padding:22px 20px; display:flex; align-items:center; gap:16px; box-shadow:var(--shadow-sm);">
      <div style="width:52px; height:52px; border-radius:12px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:22px;">
        <i class="fa-solid fa-ticket"></i>
      </div>
      <div>
        <span style="font-size:11.5px; color:var(--charcoal-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">Passes Reserved</span>
        <h3 style="font-size:24px; margin:2px 0 0; color:var(--burgundy); font-family:var(--font-heading); font-weight:800;"><?= $totalBookings ?></h3>
      </div>
    </div>

    <div style="background:#FFFFFF; border-radius:14px; border:1.5px solid var(--gold-border); padding:22px 20px; display:flex; align-items:center; gap:16px; box-shadow:var(--shadow-sm);">
      <div style="width:52px; height:52px; border-radius:12px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:22px;">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div>
        <span style="font-size:11.5px; color:var(--charcoal-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">Cumulative Pass Value</span>
        <h3 style="font-size:24px; margin:2px 0 0; color:var(--burgundy); font-family:var(--font-heading); font-weight:800;"><?= format_price($totalSpent) ?></h3>
      </div>
    </div>

    <div style="background:#FFFFFF; border-radius:14px; border:1.5px solid var(--gold-border); padding:22px 20px; display:flex; align-items:center; gap:16px; box-shadow:var(--shadow-sm);">
      <div style="width:52px; height:52px; border-radius:12px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:22px;">
        <i class="fa-regular fa-calendar-check"></i>
      </div>
      <div>
        <span style="font-size:11.5px; color:var(--charcoal-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">Member Since</span>
        <h3 style="font-size:20px; margin:2px 0 0; color:var(--burgundy); font-family:var(--font-heading); font-weight:800;"><?= date('F Y', strtotime($user['created_at'])) ?></h3>
      </div>
    </div>

    <div style="background:#FFFFFF; border-radius:14px; border:1.5px solid var(--gold-border); padding:22px 20px; display:flex; align-items:center; gap:16px; box-shadow:var(--shadow-sm);">
      <div style="width:52px; height:52px; border-radius:12px; background:var(--ivory-soft); color:#10B981; border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:22px;">
        <i class="fa-solid fa-shield-check"></i>
      </div>
      <div>
        <span style="font-size:11.5px; color:var(--charcoal-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">Security Status</span>
        <h3 style="font-size:18px; margin:2px 0 0; color:#065F46; font-weight:700;">Verified Active</h3>
      </div>
    </div>

  </div>

  <div class="contact-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:32px; align-items:start;">
    
    <!-- Edit Profile Card -->
    <div style="background:#FFFFFF; border-radius:20px; border:1.5px solid var(--gold-border); padding:36px 32px; box-shadow:var(--shadow-sm);">
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
        <div style="width:40px; height:40px; border-radius:10px; background:var(--burgundy-gradient); color:var(--gold-light); display:flex; align-items:center; justify-content:center; font-size:18px; border:1px solid var(--gold);">
          <i class="fa-regular fa-id-card"></i>
        </div>
        <h3 style="font-size:22px; margin:0; color:var(--burgundy); font-family:var(--font-heading); font-weight:800;">
          Personal Information
        </h3>
      </div>
      <div class="gold-divider" style="margin:10px 0 20px 0; justify-content:flex-start;"></div>
      <p style="font-size:13.5px; color:var(--charcoal-muted); margin-bottom:24px;">
        Update your royal member patron name and direct contact coordinates.
      </p>

      <form action="profile.php" method="POST">
        <input type="hidden" name="action" value="update_profile">

        <div class="form-group">
          <label for="fullname"><i class="fa-solid fa-user"></i> Full Name *</label>
          <input type="text" id="fullname" name="fullname" class="form-control" value="<?= e($user['fullname']) ?>" required>
        </div>

        <div class="form-group">
          <label for="email"><i class="fa-solid fa-envelope"></i> Registered Email Address</label>
          <input type="email" id="email" class="form-control" value="<?= e($user['email']) ?>" readonly style="background:var(--ivory-soft); color:var(--charcoal-muted); cursor:not-allowed;">
          <small style="color:var(--charcoal-muted); font-size:11.5px; margin-top:4px;">Email address cannot be changed once authenticated.</small>
        </div>

        <div class="form-group">
          <label for="phone"><i class="fa-solid fa-phone"></i> Phone / WhatsApp Number</label>
          <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($user['phone']) ?>" placeholder="+91 98256 61046">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:13px; font-size:15px; font-weight:700; border-radius:10px; margin-top:10px; box-shadow:0 6px 20px rgba(122,28,46,0.3);">
          <i class="fa-solid fa-floppy-disk"></i> Save Profile Details
        </button>
      </form>
    </div>

    <!-- Change Password Card -->
    <div style="background:#FFFFFF; border-radius:20px; border:1.5px solid var(--gold-border); padding:36px 32px; box-shadow:var(--shadow-sm);">
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
        <div style="width:40px; height:40px; border-radius:10px; background:var(--burgundy-gradient); color:var(--gold-light); display:flex; align-items:center; justify-content:center; font-size:18px; border:1px solid var(--gold);">
          <i class="fa-solid fa-key"></i>
        </div>
        <h3 style="font-size:22px; margin:0; color:var(--burgundy); font-family:var(--font-heading); font-weight:800;">
          Security Password
        </h3>
      </div>
      <div class="gold-divider" style="margin:10px 0 20px 0; justify-content:flex-start;"></div>
      <p style="font-size:13.5px; color:var(--charcoal-muted); margin-bottom:24px;">
        Ensure your account is protected with a 256-bit encrypted master password.
      </p>

      <form action="profile.php" method="POST">
        <input type="hidden" name="action" value="change_password">

        <div class="form-group">
          <label for="current_password"><i class="fa-solid fa-lock"></i> Current Password *</label>
          <div class="password-input-wrap" style="position:relative;">
            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter existing password" required>
            <button type="button" class="password-toggle-btn" onclick="togglePassField('current_password', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94A3B8; cursor:pointer;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="new_password"><i class="fa-solid fa-shield-halved"></i> New Password (min 6 characters) *</label>
          <div class="password-input-wrap" style="position:relative;">
            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Create strong new password" required minlength="6">
            <button type="button" class="password-toggle-btn" onclick="togglePassField('new_password', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94A3B8; cursor:pointer;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_password"><i class="fa-solid fa-check-double"></i> Confirm New Password *</label>
          <div class="password-input-wrap" style="position:relative;">
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required minlength="6">
            <button type="button" class="password-toggle-btn" onclick="togglePassField('confirm_password', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94A3B8; cursor:pointer;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-secondary" style="width:100%; padding:13px; font-size:15px; font-weight:700; border-radius:10px; margin-top:10px;">
          <i class="fa-solid fa-key"></i> Update Security Password
        </button>
      </form>
    </div>

  </div>

  <!-- Direct Concierge Help Strip -->
  <div style="margin-top:35px; background:#FFFFFF; border:1.5px solid var(--gold-border); border-radius:16px; padding:22px 28px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div style="display:flex; align-items:center; gap:14px;">
      <div style="width:44px; height:44px; border-radius:10px; background:var(--ivory-soft); color:#10B981; display:flex; align-items:center; justify-content:center; font-size:22px; border:1px solid var(--gold-border);">
        <i class="fa-brands fa-whatsapp"></i>
      </div>
      <div>
        <h5 style="margin:0 0 2px; color:var(--burgundy); font-size:15px; font-family:var(--font-heading);">Need Help with Your Account or Event Bookings?</h5>
        <p style="margin:0; font-size:12.5px; color:var(--charcoal-muted);">Our Gondal atelier team is active Mon–Sat 10:00 AM – 8:30 PM</p>
      </div>
    </div>
    <a href="https://wa.me/919825661046?text=<?= urlencode('Namaste Bhakti Events! I need assistance regarding my member account profile.') ?>" 
       target="_blank" 
       rel="noopener" 
       class="btn btn-whatsapp" 
       style="padding:10px 18px; font-size:13.5px; border-radius:8px;">
      <i class="fa-brands fa-whatsapp"></i> Chat with Concierge
    </a>
  </div>

</div>

<script>
function togglePassField(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector('i');
  if (input && icon) {
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    icon.className = isPass ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
  }
}
</script>

<?php include '../includes/footer.php'; ?>

