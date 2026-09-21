<?php
// admin/manage-users.php - Royal Guest & User Directory
require_once '../config/db.php';
require_once '../includes/functions.php';

// Handle Role Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_role') {
    $targetUserId = intval($_POST['user_id'] ?? 0);
    $currentRole  = $_POST['current_role'] ?? 'user';
    $newRole      = ($currentRole === 'admin') ? 'user' : 'admin';

    // Prevent demoting self
    if ($targetUserId === $_SESSION['user_id'] && $newRole === 'user') {
        set_flash('danger', 'You cannot revoke your own administrator privileges.');
    } elseif ($targetUserId > 0) {
        $upStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $upStmt->execute([$newRole, $targetUserId]);
        set_flash('success', "User role successfully updated to " . ucfirst($newRole) . ".");
    }
    header("Location: manage-users.php");
    exit;
}

// Fetch users with booking counts
$sql = "SELECT u.*, COUNT(b.id) AS total_bookings 
        FROM users u 
        LEFT JOIN bookings b ON u.id = b.user_id 
        GROUP BY u.id 
        ORDER BY u.created_at DESC";
$users = $pdo->query($sql)->fetchAll();

$pageTitle = "Guest Directory";
$pageHeader = "Registered Members & System Access";
include 'sidebar.php';
?>


<div class="admin-card">
  <div class="admin-card-header">
    <div style="display:flex; align-items:center; gap:16px;">
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid fa-users-gear me-2" style="color:var(--gold);"></i> All Registered Members (<?= count($users) ?>)
      </h3>
    </div>
    <div class="admin-toolbar">
      <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="tableSearch" placeholder="Search member, phone or email..." class="search-input" style="width:300px;">
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Member</th>
          <th>Contact Info</th>
          <th>System Role</th>
          <th style="text-align:center;">Passes</th>
          <th>Member Since</th>
          <th style="text-align:right;">Access &amp; Connect</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <?php 
            $userPhone = preg_replace('/[^0-9]/', '', $u['phone'] ?? '');
            if (!empty($userPhone) && strlen($userPhone) === 10) {
                $userPhone = '91' . $userPhone;
            }
          ?>
          <tr>
            <td class="nowrap">
              <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; border-radius:50%; background:<?= $u['role'] === 'admin' ? 'var(--burgundy)' : 'var(--ivory-soft)' ?>; color:<?= $u['role'] === 'admin' ? 'var(--gold-light)' : 'var(--burgundy)' ?>; border:1.5px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                  <?= strtoupper(substr($u['fullname'], 0, 1)) ?>
                </div>
                <div>
                  <strong style="color:var(--burgundy); font-size:14.5px; display:block;">
                    <?= e($u['fullname']) ?>
                  </strong>
                  <span style="font-size:11.5px; color:var(--charcoal-muted);">Member ID #<?= $u['id'] ?></span>
                </div>
              </div>
            </td>
            <td>
              <div style="font-size:13.5px; color:var(--charcoal);">
                <i class="fa-regular fa-envelope me-1" style="color:var(--gold);"></i> <?= e($u['email']) ?>
              </div>
              <?php if (!empty($u['phone'])): ?>
                <div style="font-size:12px; color:var(--charcoal-muted); margin-top:2px;">
                  <i class="fa-solid fa-phone me-1" style="color:var(--gold);"></i> <?= e($u['phone']) ?>
                </div>
              <?php endif; ?>
            </td>
            <td class="nowrap">
              <span class="status-badge <?= $u['role'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?>">
                <i class="fa-solid <?= $u['role'] === 'admin' ? 'fa-shield-halved' : 'fa-user' ?> me-1"></i>
                <?= ucfirst($u['role']) ?>
              </span>
            </td>
            <td class="nowrap" style="text-align:center;">
              <span style="display:inline-block; font-weight:800; font-size:13px; background:#FAF5EE; color:var(--burgundy); padding:3px 10px; border-radius:12px; border:1px solid var(--gold-border);">
                <?= $u['total_bookings'] ?> <?= $u['total_bookings'] == 1 ? 'Pass' : 'Passes' ?>
              </span>
            </td>
            <td class="nowrap" style="font-size:13px; color:var(--charcoal-muted);">
              <?= date('M d, Y', strtotime($u['created_at'])) ?>
            </td>
            <td style="text-align:right;" class="nowrap">
              <div style="display:inline-flex; gap:8px; align-items:center; justify-content:flex-end;">
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                  <form action="manage-users.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Change role for <?= e($u['fullname']) ?> to <?= $u['role'] === 'admin' ? 'Member' : 'Administrator' ?>?')">
                    <input type="hidden" name="action" value="toggle_role">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="current_role" value="<?= $u['role'] ?>">
                    <?php if ($u['role'] === 'admin'): ?>
                      <button type="submit" class="btn-demote-role" title="Demote to Standard Member">
                        <i class="fa-solid fa-user-minus"></i> Demote
                      </button>
                    <?php else: ?>
                      <button type="submit" class="btn-royal-role" title="Promote to Royal Administrator">
                        <i class="fa-solid fa-crown" style="color:var(--gold);"></i> Make Admin
                      </button>
                    <?php endif; ?>
                  </form>
                <?php else: ?>
                  <span class="badge-self-admin" title="Your Active Session">
                    <i class="fa-solid fa-circle-check"></i> You (Active)
                  </span>
                <?php endif; ?>

                <?php if (!empty($userPhone)): ?>
                  <a href="https://wa.me/<?= $userPhone ?>?text=<?= urlencode('Hello ' . $u['fullname'] . ', greetings from Bhakti Events & Celebrations Gondal!') ?>" 
                     target="_blank" 
                     rel="noopener" 
                     class="btn btn-sm btn-whatsapp" 
                     style="padding:5px 10px; font-size:11.5px;"
                     title="WhatsApp Direct Message">
                    <i class="fa-brands fa-whatsapp"></i> Chat
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.php'; ?>
