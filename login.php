<?php
// login.php - Secure User & Royal Concierge Authentication
require_once 'config/db.php';
require_once 'includes/functions.php';

// Handle account switching
if (isset($_GET['switch'])) {
    if ($_GET['switch'] === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $adminUser = $stmt->fetch();
        if ($adminUser) {
            $_SESSION['user_id']  = $adminUser['id'];
            $_SESSION['fullname'] = $adminUser['fullname'];
            $_SESSION['email']    = $adminUser['email'];
            $_SESSION['phone']    = $adminUser['phone'];
            $_SESSION['role']     = 'admin';
            set_flash('success', 'Switched successfully to Royal Administrator Console.');
            header("Location: admin/dashboard.php");
            exit;
        }
    } else {
        // Clear active session to sign in fresh
        $_SESSION = [];
        if (session_id() !== '') {
            session_destroy();
            session_start();
        }
    }
}

// If already admin, redirect straight to console
if (is_admin()) {
    header("Location: admin/dashboard.php");
    exit;
}

$pageTitle = "Sign In";
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash('danger', 'Please enter both your registered email address and password.');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password matches! Initialize authenticated session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['phone']    = $user['phone'];
            $_SESSION['role']     = $user['role'];

            set_flash('success', 'Welcome back, ' . $user['fullname'] . '! Your royal session is active.');

            // Redirect based on role or original request
            if ($user['role'] === 'admin' && ($redirect === 'index.php' || empty($redirect))) {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: " . filter_var($redirect, FILTER_SANITIZE_URL));
            }
            exit;
        } else {
            set_flash('danger', 'Invalid email address or password. Please verify your credentials or use the demonstration keys below.');
        }
    }
}

include 'includes/header.php';

?>

<!-- Royal Authentication Section -->
<section class="auth-section">
  <div class="auth-container">
    <div class="auth-master-card">
      
      <!-- Left Royal Showcase Panel -->
      <div class="auth-showcase-panel">
        <!-- Subtle Watermark Crest -->
        <div class="auth-showcase-watermark">
          <i class="fa-solid fa-crown"></i>
        </div>

        <div class="auth-showcase-content">
          <div class="auth-brand-badge">
            <i class="fa-solid fa-crown" style="color:var(--gold-light);"></i>
            Royal Member &bull; Est. 2014 &bull; Gondal
          </div>

          <h2 class="auth-showcase-title">
            Step Into a World of <span>Regal Splendor</span>
          </h2>
          <p class="auth-showcase-desc">
            Sign in to your private portal to manage royal wedding reservations, download cryptographically verified digital passes, and track turnkey celebration directorships.
          </p>

          <!-- Royal Perks List -->
          <div class="auth-perks-list">
            <div class="auth-perk-item">
              <div class="auth-perk-icon">
                <i class="fa-solid fa-qrcode"></i>
              </div>
              <div class="auth-perk-text">
                <strong>Instant QR Pass Concierge</strong>
                <p>Real-time digital pass vouchers with QR barcode validation for fast gate access.</p>
              </div>
            </div>

            <div class="auth-perk-item">
              <div class="auth-perk-icon">
                <i class="fa-solid fa-gem"></i>
              </div>
              <div class="auth-perk-text">
                <strong>Bespoke Celebration Tracker</strong>
                <p>Track floral mandap designs, guest hospitality tiers, and banquet schedules.</p>
              </div>
            </div>

            <div class="auth-perk-item">
              <div class="auth-perk-icon">
                <i class="fa-solid fa-headset"></i>
              </div>
              <div class="auth-perk-text">
                <strong>Direct Atelier Concierge</strong>
                <p>Priority communication with our senior event directors based in Gondal.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Royal Patron Testimonial Quote -->
        <div class="auth-quote-card">
          &ldquo;Bhakti Events orchestrated our Rajputana palace celebration in Udaipur with breathtaking grandeur and turnkey poise.&rdquo;
          <span class="auth-quote-author">&mdash; Maharawal R. Singh, Udaipur Royal Matrimony</span>
        </div>
      </div>

      <!-- Right Form Panel -->
      <div class="auth-form-panel">
        <a href="index.php" class="auth-back-link">
          <i class="fa-solid fa-arrow-left"></i> Return to Royal Gallery
        </a>

        <div class="auth-header-text">
          <h2>Welcome to Royal Portal</h2>
          <p>Access your reserved celebration passes and member preferences</p>
        </div>

        <?php if (is_logged_in()): ?>
          <div style="background:var(--ivory-soft); border:1.5px solid var(--gold-border); border-radius:12px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:var(--burgundy); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
              <i class="fa-solid fa-circle-user me-1" style="color:var(--gold);"></i> Active: <strong><?= e($_SESSION['fullname']) ?></strong> (Member)
            </div>
            <div style="display:flex; gap:8px;">
              <a href="login.php?switch=admin" class="btn btn-sm btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none;">
                <i class="fa-solid fa-crown me-1"></i> Switch to Admin
              </a>
              <a href="logout.php" class="btn btn-sm" style="padding:6px 10px; font-size:12px; background:#FEE2E2; color:#991B1B; text-decoration:none;">
                Sign Out
              </a>
            </div>
          </div>
        <?php endif; ?>


        <form action="login.php" method="POST" id="royalLoginForm">
          <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

          <!-- Email Address -->
          <div class="form-group">
            <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   class="form-control" 
                   placeholder="e.g. maharawal@bhaktievents.com" 
                   required 
                   autofocus 
                   value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>">
          </div>

          <!-- Password with Toggle -->
          <div class="form-group">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label for="password" style="margin-bottom:0;"><i class="fa-solid fa-key"></i> Security Password</label>
              <a href="https://wa.me/919825661046?text=<?= urlencode('Namaste Bhakti Events! I need assistance recovering my portal credentials.') ?>" 
                 target="_blank" 
                 rel="noopener"
                 style="font-size:12px; color:var(--burgundy); font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <i class="fa-brands fa-whatsapp" style="color:#10B981;"></i> Forgot Password?
              </a>
            </div>
            <div class="password-input-wrap">
              <input type="password" 
                     id="password" 
                     name="password" 
                     class="form-control" 
                     placeholder="Enter your confidential password" 
                     required>
              <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Toggle password visibility">
                <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me Checkbox -->
          <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--charcoal); cursor:pointer; margin:0; user-select:none;">
              <input type="checkbox" name="remember" value="1" checked style="accent-color:var(--burgundy); width:16px; height:16px; cursor:pointer;">
              Keep me signed in on this workstation
            </label>
          </div>

          <!-- Sign In Submit Button -->
          <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15.5px; border-radius:12px; font-weight:700; box-shadow:0 8px 25px rgba(122,28,46,0.32); display:flex; align-items:center; justify-content:center; gap:9px;">
            <i class="fa-solid fa-right-to-bracket"></i> Sign In to Royal Portal
          </button>

          <!-- Fast-Access Demonstration Accounts Bar -->
          <div class="demo-access-box">
            <div class="demo-access-header">
              <div class="demo-access-title">
                <i class="fa-solid fa-bolt"></i> Fast-Access Demonstration Keys
              </div>
              <span class="demo-badge-eval"><i class="fa-solid fa-graduation-cap"></i> Evaluator Ready</span>
            </div>
            <p class="demo-access-sub">One-click auto-fill credentials for academic evaluation &amp; testing:</p>
            
            <div class="demo-buttons-row">
              <a href="login.php?switch=admin" class="demo-key-btn" style="text-decoration:none;">
                <i class="fa-solid fa-crown" style="color:var(--gold);"></i> 1-Click Sign In as Admin
              </a>
              <button type="button" class="demo-key-btn" id="btnUserFill" onclick="fillCredentials('student')">
                <i class="fa-solid fa-ticket" style="color:var(--burgundy);"></i> Auto-fill Member Key
              </button>
            </div>

            <div id="fillFeedback" style="display:none; font-size:11.5px; color:#065F46; margin-top:8px; text-align:center; font-weight:600;">
              <i class="fa-solid fa-circle-check me-1" style="color:#10B981;"></i> Demonstration credentials applied! Click &ldquo;Sign In to Royal Portal&rdquo; to enter.
            </div>
          </div>
        </form>

        <!-- Link to Register -->
        <div style="text-align:center; margin-top:22px; padding-top:16px; border-top:1px solid rgba(0,0,0,0.06); font-size:13.5px; color:var(--charcoal-muted);">
          Don&rsquo;t have an account yet? 
          <a href="register.php" style="font-weight:700; color:var(--burgundy); text-decoration:none; margin-left:3px;">
            Register Member Account &rarr;
          </a>
        </div>

        <!-- Security Trust Strip -->
        <div class="auth-trust-strip">
          <div class="auth-trust-item">
            <i class="fa-solid fa-lock" style="color:var(--gold);"></i> 256-Bit SSL Encrypted
          </div>
          <div class="auth-trust-item">
            <i class="fa-solid fa-shield-halved" style="color:var(--gold);"></i> Role-Based Access
          </div>
          <div class="auth-trust-item">
            <i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Gondal Atelier
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Interactive Script for Password Toggle & Demo Buttons Feedback -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const toggleIcon = document.getElementById('togglePasswordIcon');

  if (toggleBtn && passwordInput && toggleIcon) {
    toggleBtn.addEventListener('click', function() {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      toggleIcon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });
  }
});

// Override fillCredentials to also trigger visual feedback
const originalFillCredentials = window.fillCredentials;
window.fillCredentials = function(role) {
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const feedback = document.getElementById('fillFeedback');
  const btnAdmin = document.getElementById('btnAdminFill');
  const btnUser = document.getElementById('btnUserFill');

  if (role === 'admin') {
    if (emailInput) emailInput.value = 'admin@eventsphere.com';
    if (passwordInput) passwordInput.value = 'password123';
    if (btnAdmin) btnAdmin.classList.add('active-loaded');
    if (btnUser) btnUser.classList.remove('active-loaded');
  } else {
    if (emailInput) emailInput.value = 'ankita@gmail.com';
    if (passwordInput) passwordInput.value = 'password123';
    if (btnUser) btnUser.classList.add('active-loaded');
    if (btnAdmin) btnAdmin.classList.remove('active-loaded');
  }

  if (feedback) {
    feedback.style.display = 'block';
  }
};
</script>

<?php include 'includes/footer.php'; ?>

