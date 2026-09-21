<?php
// admin/manage-inquiries.php - Royal Client Consultation & Inquiry Management
require_once '../config/db.php';
require_once '../includes/functions.php';

require_admin('../login.php?switch=admin');

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $inquiryId = intval($_POST['inquiry_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $allowedStatuses = ['New', 'Contacted', 'Proposal Sent', 'Confirmed', 'Closed'];

        if ($inquiryId > 0 && in_array($newStatus, $allowedStatuses)) {
            $upStmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
            $upStmt->execute([$newStatus, $inquiryId]);
            set_flash('success', "Inquiry status updated to '$newStatus'.");
        }
        header("Location: manage-inquiries.php");
        exit;
    }

    if ($_POST['action'] === 'delete_inquiry') {
        $inquiryId = intval($_POST['inquiry_id'] ?? 0);
        if ($inquiryId > 0) {
            $delStmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
            $delStmt->execute([$inquiryId]);
            set_flash('success', "Inquiry record removed from database.");
        }
        header("Location: manage-inquiries.php");
        exit;
    }
}

// Filter and Search
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$sql = "SELECT * FROM inquiries WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ? OR inquiry_ref LIKE ? OR service LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

// Counts for KPI summary
$totalInquiries = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$newInqCount = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'New'")->fetchColumn();
$contactedCount = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Contacted'")->fetchColumn();
$proposalCount = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Proposal Sent'")->fetchColumn();

$pageTitle  = "Client Inquiries";
$pageHeader = "Royal Inquiries & Consultation Directorship";
include 'sidebar.php';
?>

<!-- KPI Stat Cards -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-details">
      <span>Total Inquiries</span>
      <h3><?= $totalInquiries ?></h3>
      <div style="font-size:11.5px; color:var(--charcoal-muted); margin-top:2px;">All-time requests</div>
    </div>
    <div class="kpi-icon indigo">
      <i class="fa-solid fa-feather-pointed"></i>
    </div>
  </div>

  <div class="kpi-card" style="border-top-color: #F59E0B;">
    <div class="kpi-details">
      <span>New Inquiries</span>
      <h3 style="color:#B45309;"><?= $newInqCount ?></h3>
      <div style="font-size:11.5px; color:#B45309; font-weight:700; margin-top:2px;">Awaiting Concierge</div>
    </div>
    <div class="kpi-icon amber">
      <i class="fa-solid fa-bell"></i>
    </div>
  </div>

  <div class="kpi-card" style="border-top-color: #0284C7;">
    <div class="kpi-details">
      <span>In Discussion</span>
      <h3 style="color:#0284C7;"><?= $contactedCount ?></h3>
      <div style="font-size:11.5px; color:var(--charcoal-muted); margin-top:2px;">Scope finalizing</div>
    </div>
    <div class="kpi-icon cyan">
      <i class="fa-solid fa-comments"></i>
    </div>
  </div>

  <div class="kpi-card" style="border-top-color: #059669;">
    <div class="kpi-details">
      <span>Proposals Sent</span>
      <h3 style="color:#059669;"><?= $proposalCount ?></h3>
      <div style="font-size:11.5px; color:#059669; font-weight:700; margin-top:2px;">Estimates active</div>
    </div>
    <div class="kpi-icon emerald">
      <i class="fa-solid fa-file-invoice-dollar"></i>
    </div>
  </div>
</div>

<!-- Main Table Container -->
<div class="admin-card">
  <div class="admin-card-header">
    <div style="display:flex; align-items:center; gap:16px;">
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid fa-envelope-open-text me-2" style="color:var(--gold);"></i> Patron Consultation Requests (<?= count($inquiries) ?>)
      </h3>
    </div>

    <!-- Filter & Search Controls in a single aligned toolbar -->
    <form method="GET" action="manage-inquiries.php" class="admin-toolbar" style="margin:0;">
      <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" 
               name="search" 
               class="search-input" 
               placeholder="Search patron, phone, ref..." 
               value="<?= e($search) ?>" 
               style="width:250px;">
      </div>

      <select name="status" class="select-filter" style="width:150px;" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="New" <?= $statusFilter === 'New' ? 'selected' : '' ?>>New</option>
        <option value="Contacted" <?= $statusFilter === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
        <option value="Proposal Sent" <?= $statusFilter === 'Proposal Sent' ? 'selected' : '' ?>>Proposal Sent</option>
        <option value="Confirmed" <?= $statusFilter === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
        <option value="Closed" <?= $statusFilter === 'Closed' ? 'selected' : '' ?>>Closed</option>
      </select>

      <button type="submit" class="btn btn-sm btn-primary toolbar-btn">
        <i class="fa-solid fa-filter"></i> Filter
      </button>

      <?php if (!empty($search) || !empty($statusFilter)): ?>
        <a href="manage-inquiries.php" class="btn btn-sm btn-outline-primary toolbar-btn" style="border-color:var(--gold); color:var(--burgundy);">
          <i class="fa-solid fa-rotate-left"></i> Reset
        </a>
      <?php endif; ?>
    </form>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref &amp; Date</th>
          <th>Patron Coordinates</th>
          <th>Celebration Scope</th>
          <th>Venue &amp; Attendance</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($inquiries)): ?>
          <?php foreach ($inquiries as $inq): ?>
            <tr>
              <!-- Ref & Timestamp -->
              <td class="nowrap">
                <span class="ref-code" style="cursor:pointer;" onclick="viewInquiry(<?= htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8') ?>)" title="View Consultation Vision">
                  <i class="fa-solid fa-feather-pointed me-1" style="font-size:11px; opacity:0.85;"></i><?= e($inq['inquiry_ref']) ?>
                </span>
                <div style="font-size:11.5px; color:var(--charcoal-muted); margin-top:4px;">
                  <i class="fa-regular fa-clock me-1" style="color:var(--gold);"></i>
                  <?= date('M d, Y h:i A', strtotime($inq['created_at'])) ?>
                </div>
              </td>

              <!-- Patron Contact -->
              <td>
                <strong style="color:var(--burgundy-dark); font-size:14px; display:block;">
                  <?= e($inq['fullname']) ?>
                </strong>
                <div style="font-size:12px; color:var(--charcoal-muted); margin-top:2px;">
                  <a href="mailto:<?= e($inq['email']) ?>" style="color:inherit; text-decoration:none;">
                    <i class="fa-regular fa-envelope me-1" style="color:var(--gold);"></i> <?= e($inq['email']) ?>
                  </a>
                </div>
                <div style="font-size:12px; color:var(--charcoal); margin-top:2px; font-weight:600;">
                  <a href="tel:<?= e($inq['phone']) ?>" style="color:inherit; text-decoration:none;">
                    <i class="fa-solid fa-phone me-1" style="color:var(--gold);"></i> <?= e($inq['phone']) ?>
                  </a>
                </div>
              </td>

              <!-- Celebration Scope -->
              <td class="nowrap">
                <span class="royal-category-pill" style="margin-bottom:5px;">
                  <i class="fa-solid fa-gem" style="font-size:10px; color:var(--gold);"></i>
                  <?= e($inq['service']) ?>
                </span>
                <div style="font-size:12px; color:var(--charcoal-muted); margin-top:3px;">
                  <i class="fa-regular fa-calendar-check me-1" style="color:var(--gold);"></i>
                  <?= !empty($inq['event_date']) ? e($inq['event_date']) : 'Flexible / TBD' ?>
                </div>
              </td>

              <!-- Venue & Guests -->
              <td>
                <div style="font-size:13px; font-weight:600; color:var(--charcoal);" title="<?= e($inq['venue']) ?>">
                  <i class="fa-solid fa-archway me-1" style="color:var(--burgundy);"></i>
                  <?= !empty($inq['venue']) ? e(substr($inq['venue'], 0, 26)) . (strlen($inq['venue']) > 26 ? '...' : '') : 'Custom Location' ?>
                </div>
                <div style="font-size:12px; color:var(--charcoal-muted); margin-top:3px;">
                  <i class="fa-solid fa-users me-1" style="color:var(--gold);"></i>
                  <?= !empty($inq['guests']) ? e($inq['guests']) : 'TBD' ?> Guests
                </div>
              </td>

              <!-- Status Dropdown Quick Change -->
              <td class="nowrap">
                <form action="manage-inquiries.php" method="POST" style="margin:0;">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                  <div class="inquiry-status-wrapper status-<?= strtolower(str_replace(' ', '-', $inq['status'])) ?>">
                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="inquiry-status-select"
                            title="Change Consultation Status">
                      <option value="New" <?= $inq['status'] === 'New' ? 'selected' : '' ?>>🌟 New</option>
                      <option value="Contacted" <?= $inq['status'] === 'Contacted' ? 'selected' : '' ?>>💬 Contacted</option>
                      <option value="Proposal Sent" <?= $inq['status'] === 'Proposal Sent' ? 'selected' : '' ?>>📄 Proposal Sent</option>
                      <option value="Confirmed" <?= $inq['status'] === 'Confirmed' ? 'selected' : '' ?>>👑 Confirmed</option>
                      <option value="Closed" <?= $inq['status'] === 'Closed' ? 'selected' : '' ?>>🔒 Closed</option>
                    </select>
                  </div>
                </form>
              </td>

              <!-- Actions -->
              <td style="text-align:right;" class="nowrap">
                <div class="action-btns" style="justify-content:flex-end;">
                  <!-- WhatsApp Client Reply -->
                  <?php 
                  $cleanPhone = preg_replace('/[^0-9]/', '', $inq['phone']);
                  if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;
                  $waMsg = "Namaste " . $inq['fullname'] . "! Greetings from Bhakti Events & Celebrations, Gondal. We have received your consultation inquiry (" . $inq['inquiry_ref'] . ") regarding '" . $inq['service'] . "'. Our senior event director would be delighted to discuss your celebration vision.";
                  $waLink = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($waMsg);
                  ?>
                  <a href="<?= $waLink ?>" target="_blank" rel="noopener" class="btn-icon whatsapp" title="Chat Directly on WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                  </a>

                  <!-- View Modal Details Trigger -->
                  <button type="button" class="btn-icon view" onclick="viewInquiry(<?= htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8') ?>)" title="View Full Celebration Vision">
                    <i class="fa-solid fa-eye"></i>
                  </button>

                  <!-- Delete Record -->
                  <form action="manage-inquiries.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Permanently delete consultation inquiry <?= e($inq['inquiry_ref']) ?>?')">
                    <input type="hidden" name="action" value="delete_inquiry">
                    <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                    <button type="submit" class="btn-icon delete" title="Delete Inquiry Record">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:45px 20px; color:var(--charcoal-muted);">
              <i class="fa-solid fa-inbox" style="font-size:32px; color:var(--gold); display:block; margin-bottom:10px;"></i>
              No consultation inquiries found matching your filters.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: View Full Celebration Inquiry Details -->
<div id="inquiryModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:1100; align-items:center; justify-content:center;">
  <div style="background:#FFFFFF; border-radius:18px; border:1.5px solid var(--gold-border); max-width:640px; width:92%; padding:30px; box-shadow:0 25px 60px rgba(0,0,0,0.4); max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--gold-border); padding-bottom:14px; margin-bottom:18px;">
      <h3 style="margin:0; font-family:var(--font-heading); color:var(--burgundy); font-size:22px;">
        <i class="fa-solid fa-crown" style="color:var(--gold);"></i> Royal Consultation Details
      </h3>
      <button type="button" onclick="closeInquiryModal()" style="background:none; border:none; font-size:22px; cursor:pointer; color:var(--charcoal-muted);">&times;</button>
    </div>

    <div id="modalBodyContent"></div>

    <div style="margin-top:24px; padding-top:16px; border-top:1px solid var(--gold-border); display:flex; justify-content:flex-end; gap:10px;">
      <a id="modalWaBtn" href="#" target="_blank" rel="noopener" class="btn btn-whatsapp" style="font-size:13.5px; padding:10px 16px;">
        <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
      </a>
      <button type="button" onclick="closeInquiryModal()" class="btn btn-secondary" style="font-size:13.5px; padding:10px 16px;">
        Close Window
      </button>
    </div>
  </div>
</div>

<script>
function viewInquiry(data) {
  const modal = document.getElementById('inquiryModal');
  const body = document.getElementById('modalBodyContent');
  const waBtn = document.getElementById('modalWaBtn');

  if (!modal || !body) return;

  const waMsg = encodeURIComponent(`Namaste ${data.fullname}! Greetings from Bhakti Events Gondal regarding your inquiry (${data.inquiry_ref}) for '${data.service}'.`);
  waBtn.href = `https://wa.me/${data.phone.replace(/[^0-9]/g, '')}?text=${waMsg}`;

  body.innerHTML = `
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
      <div><span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase;">Inquiry Reference:</span><br><strong style="color:var(--burgundy); font-family:monospace; font-size:15px;">${data.inquiry_ref}</strong></div>
      <div><span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase;">Received On:</span><br><strong style="color:var(--charcoal); font-size:13.5px;">${data.created_at}</strong></div>
      <div><span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase;">Patron Name:</span><br><strong style="color:var(--burgundy); font-size:14.5px;">${data.fullname}</strong></div>
      <div><span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase;">Phone / WhatsApp:</span><br><strong style="color:var(--charcoal); font-size:14px;">${data.phone}</strong></div>
      <div><span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase;">Email Address:</span><br><strong style="color:var(--charcoal); font-size:13.5px;">${data.email}</strong></div>
      <div><span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase;">Current Status:</span><br><strong style="color:var(--burgundy); font-size:13.5px;">${data.status}</strong></div>
    </div>

    <div style="background:var(--ivory-soft); border:1px solid var(--gold-border); border-radius:10px; padding:12px 16px; margin-bottom:16px;">
      <div style="margin-bottom:6px;"><strong style="color:var(--burgundy);">Celebration Type:</strong> ${data.service}</div>
      <div style="margin-bottom:6px;"><strong style="color:var(--burgundy);">Tentative Date / Season:</strong> ${data.event_date || 'Flexible'}</div>
      <div style="margin-bottom:6px;"><strong style="color:var(--burgundy);">Venue Tier:</strong> ${data.venue || 'Private Venue'}</div>
      <div><strong style="color:var(--burgundy);">Estimated Attendance:</strong> ${data.guests || 'Unspecified'}</div>
    </div>

    <div>
      <span style="font-size:11px; color:var(--charcoal-muted); text-transform:uppercase; font-weight:700; display:block; margin-bottom:4px;">
        Celebration Vision &amp; Special Requirements:
      </span>
      <div style="background:#FFFFFF; border:1px solid rgba(0,0,0,0.1); border-radius:8px; padding:14px; font-size:13.5px; line-height:1.6; color:var(--charcoal); white-space:pre-wrap;">${data.message}</div>
    </div>
  `;

  modal.style.display = 'flex';
}

function closeInquiryModal() {
  const modal = document.getElementById('inquiryModal');
  if (modal) modal.style.display = 'none';
}

// Close on escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeInquiryModal();
});
</script>

<?php require_once 'footer.php'; ?>
