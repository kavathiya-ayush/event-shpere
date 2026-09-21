<?php
// register.php - Royal Patron Registration Portal
require_once 'config/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$pageTitle = "Register";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        set_flash('danger', 'Please fill in all mandatory fields.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Please provide a valid email address.');
    } elseif (strlen($password) < 6) {
        set_flash('danger', 'Password must be at least 6 characters long.');
    } elseif ($password !== $confirm) {
        set_flash('danger', 'Password and confirmation password do not match.');
    } else {
        // Check if email already registered
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->fetch()) {
            set_flash('danger', 'This email address is already registered. Please sign in instead.');
        } else {
            // Hash password securely with Bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $insertStmt = $pdo->prepare("INSERT INTO users (fullname, email, password, phone, role) VALUES (?, ?, ?, ?, 'user')");
            $insertStmt->execute([$fullname, $email, $hashedPassword, $phone]);
            $newUserId = $pdo->lastInsertId();

            // Auto-login newly registered user
            $_SESSION['user_id']  = $newUserId;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email']    = $email;
            $_SESSION['phone']    = $phone;
            $_SESSION['role']     = 'user';

            set_flash('success', 'Account created successfully! Welcome to Bhakti Events & Celebrations.');
            header("Location: index.php");
            exit;
        }
    }
}

include 'includes/header.php';
?>

<!-- Royal Registration Section -->
<section class="auth-section">
  <div class="auth-container">
    <div class="auth-master-card">
      
      <!-- Left Royal Showcase Panel -->
      <div class="auth-showcase-panel">
        <div class="auth-showcase-watermark">
          <i class="fa-solid fa-crown"></i>
        </div>

        <div class="auth-showcase-content">
          <div class="auth-brand-badge">
            <i class="fa-solid fa-crown" style="color:var(--gold-light);"></i>
            Royal Membership &bull; Est. 2014 &bull; Gondal
          </div>

          <h2 class="auth-showcase-title">
            Begin Your <span>Royal Journey</span>
          </h2>
          <p class="auth-showcase-desc">
            Register as a distinguished member of Bhakti Events to reserve palace banquet seats, acquire verified digital E-Passes, and access bespoke concierge consultation.
          </p>

          <!-- Perks List -->
          <div class="auth-perks-list">
            <div class="auth-perk-item">
              <div class="auth-perk-icon">
                <i class="fa-solid fa-id-card-clip"></i>
              </div>
              <div class="auth-perk-text">
                <strong>Personalized Pass Portfolio</strong>
                <p>Access and print all booked festival passes, Sufi mehfil seats, and wedding invitations.</p>
              </div>
            </div>

            <div class="auth-perk-item">
              <div class="auth-perk-icon">
                <i class="fa-solid fa-bell"></i>
              </div>
              <div class="auth-perk-text">
                <strong>Priority Early Access</strong>
                <p>Exclusive 24-hour advance booking window for high-demand Navratri Garba passes.</p>
              </div>
            </div>

            <div class="auth-perk-item">
              <div class="auth-perk-icon">
                <i class="fa-solid fa-champagne-glasses"></i>
              </div>
              <div class="auth-perk-text">
                <strong>Private Studio Tastings</strong>
                <p>Schedule one-on-one decor and menu tastings at our Gondal flagship design studio.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Trust Note -->
        <div class="auth-quote-card">
          &ldquo;Our commitment is preserving authentic Gujarati and Rajputana ceremonial grace through state-of-the-art digital hospitality.&rdquo;
          <span class="auth-quote-author">&mdash; Bhakti Events Directorship &bull; Gondal, Gujarat</span>
        </div>
      </div>

      <!-- Right Form Panel -->
      <div class="auth-form-panel">
        <a href="index.php" class="auth-back-link">
          <i class="fa-solid fa-arrow-left"></i> Return to Royal Gallery
        </a>

        <div class="auth-header-text">
          <h2>Create Royal Account</h2>
          <p>Register below to manage passes and bespoke celebration bookings</p>
        </div>

        <form action="register.php" method="POST" id="royalRegisterForm">
          <!-- Full Name -->
          <div class="form-group">
            <label for="fullname"><i class="fa-solid fa-user"></i> Full Name *</label>
            <input type="text" 
                   id="fullname" 
                   name="fullname" 
                   class="form-control" 
                   placeholder="e.g. Maharawal Rajendrasinh Jadeja" 
                   required 
                   autofocus
                   value="<?= isset($_POST['fullname']) ? e($_POST['fullname']) : '' ?>">
          </div>

          <!-- Email & Phone in 2 Columns -->
          <div class="form-row-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="form-group">
              <label for="email"><i class="fa-solid fa-envelope"></i> Email Address *</label>
              <input type="email" 
                     id="email" 
                     name="email" 
                     class="form-control" 
                     placeholder="name@domain.com" 
                     required 
                     value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
              <label for="phone"><i class="fa-solid fa-phone"></i> WhatsApp Number</label>
              <input type="tel" 
                     id="phone" 
                     name="phone" 
                     class="form-control" 
                     placeholder="+91 98256 61046" 
                     value="<?= isset($_POST['phone']) ? e($_POST['phone']) : '' ?>">
            </div>
          </div>

          <!-- Password & Confirm in 2 Columns -->
          <div class="form-row-2" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="form-group">
              <label for="reg_password"><i class="fa-solid fa-lock"></i> Password (min 6 chars) *</label>
              <div class="password-input-wrap">
                <input type="password" 
                       id="reg_password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Create a password" 
                       required 
                       minlength="6">
                <button type="button" class="password-toggle-btn" onclick="toggleField('reg_password', this)">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="form-group">
              <label for="confirm_password"><i class="fa-solid fa-check-double"></i> Confirm Password *</label>
              <div class="password-input-wrap">
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="form-control" 
                       placeholder="Confirm password" 
                       required 
                       minlength="6">
                <button type="button" class="password-toggle-btn" onclick="toggleField('confirm_password', this)">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Register Submit Button -->
          <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15.5px; border-radius:12px; font-weight:700; box-shadow:0 8px 25px rgba(122,28,46,0.32); display:flex; align-items:center; justify-content:center; gap:9px; margin-top:8px;">
            <i class="fa-solid fa-crown"></i> Register Member Account
          </button>
        </form>

        <!-- Link to Sign In -->
        <div style="text-align:center; margin-top:22px; padding-top:16px; border-top:1px solid rgba(0,0,0,0.06); font-size:13.5px; color:var(--charcoal-muted);">
          Already have an active account? 
          <a href="login.php" style="font-weight:700; color:var(--burgundy); text-decoration:none; margin-left:3px;">
            Sign In Here &rarr;
          </a>
        </div>

        <!-- Security Trust Strip -->
        <div class="auth-trust-strip">
          <div class="auth-trust-item">
            <i class="fa-solid fa-lock" style="color:var(--gold);"></i> 256-Bit SSL Encrypted
          </div>
          <div class="auth-trust-item">
            <i class="fa-solid fa-shield-halved" style="color:var(--gold);"></i> Safe &amp; Confidential
          </div>
          <div class="auth-trust-item">
            <i class="fa-solid fa-location-dot" style="color:var(--gold);"></i> Gondal Atelier
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<script>
function toggleField(fieldId, btn) {
  const input = document.getElementById(fieldId);
  const icon = btn.querySelector('i');
  if (input && icon) {
    const isPassword = input.getAttribute('type') === 'password';
    input.setAttribute('type', isPassword ? 'text' : 'password');
    icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
  }
}
</script>

<?php include 'includes/footer.php'; ?>

