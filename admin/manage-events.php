<?php
// admin/manage-events.php - Comprehensive Royal Celebration CRUD Management
require_once '../config/db.php';
require_once '../includes/functions.php';

$action = $_GET['action'] ?? 'list';
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch categories for form selects
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

// Handle Form Submissions (Create / Update / Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'create' || $postAction === 'update') {
        $title       = trim($_POST['title'] ?? '');
        $categoryId  = intval($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $venue       = trim($_POST['venue'] ?? '');
        $eventDate   = trim($_POST['event_date'] ?? '');
        $eventTime   = trim($_POST['event_time'] ?? '');
        $ticketPrice = floatval($_POST['ticket_price'] ?? 0);
        $totalSeats  = intval($_POST['total_seats'] ?? 0);
        $status      = $_POST['status'] ?? 'upcoming';

        // Validation
        if (empty($title) || $categoryId <= 0 || empty($venue) || empty($eventDate) || empty($eventTime)) {
            set_flash('danger', 'Please complete all required celebration fields.');
            header("Location: manage-events.php" . ($postAction === 'update' ? "?action=edit&id=$eventId" : "?action=create"));
            exit;
        }

        if ($ticketPrice < 0) {
            set_flash('danger', 'Pass price cannot be negative.');
            header("Location: manage-events.php?action=create");
            exit;
        }

        if ($totalSeats <= 0) {
            set_flash('danger', 'Total pass capacity must be at least 1.');
            header("Location: manage-events.php?action=create");
            exit;
        }

        // Handle Image Upload
        $imageName = 'default-event.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmp  = $_FILES['image']['tmp_name'];
            $fileName = basename($_FILES['image']['name']);
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($fileExt, $allowedExts)) {
                $newFileName = 'event_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $fileExt;
                $uploadPath = '../assets/images/events/' . $newFileName;
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $imageName = $newFileName;
                }
            }
        }

        if ($postAction === 'create') {
            $insertStmt = $pdo->prepare("INSERT INTO events 
                (category_id, title, description, venue, event_date, event_time, ticket_price, total_seats, available_seats, image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->execute([
                $categoryId, $title, $description, $venue, $eventDate, $eventTime,
                $ticketPrice, $totalSeats, $totalSeats, $imageName, $status
            ]);

            set_flash('success', 'Celebration "' . $title . '" published successfully.');
            header("Location: manage-events.php");
            exit;

        } elseif ($postAction === 'update' && $eventId > 0) {
            if ($imageName !== 'default-event.jpg') {
                $upStmt = $pdo->prepare("UPDATE events SET 
                    category_id = ?, title = ?, description = ?, venue = ?, event_date = ?, event_time = ?, 
                    ticket_price = ?, total_seats = ?, image = ?, status = ? WHERE id = ?");
                $upStmt->execute([
                    $categoryId, $title, $description, $venue, $eventDate, $eventTime,
                    $ticketPrice, $totalSeats, $imageName, $status, $eventId
                ]);
            } else {
                $upStmt = $pdo->prepare("UPDATE events SET 
                    category_id = ?, title = ?, description = ?, venue = ?, event_date = ?, event_time = ?, 
                    ticket_price = ?, total_seats = ?, status = ? WHERE id = ?");
                $upStmt->execute([
                    $categoryId, $title, $description, $venue, $eventDate, $eventTime,
                    $ticketPrice, $totalSeats, $status, $eventId
                ]);
            }

            set_flash('success', 'Celebration "' . $title . '" updated successfully.');
            header("Location: manage-events.php");
            exit;
        }
    }
}

// Handle Delete Request
if ($action === 'delete' && $eventId > 0) {
    $delStmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $delStmt->execute([$eventId]);
    set_flash('success', 'Celebration and its associated bookings have been removed.');
    header("Location: manage-events.php");
    exit;
}

// If Edit, fetch event
$editEvent = null;
if ($action === 'edit' && $eventId > 0) {
    $eStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $eStmt->execute([$eventId]);
    $editEvent = $eStmt->fetch();
    if (!$editEvent) {
        set_flash('danger', 'Celebration not found.');
        header("Location: manage-events.php");
        exit;
    }
}

// Fetch all events for the main table
$eventsStmt = $pdo->query("SELECT e.*, c.category_name 
                          FROM events e 
                          JOIN categories c ON e.category_id = c.id 
                          ORDER BY e.event_date ASC");
$allEvents = $eventsStmt->fetchAll();

$pageTitle = "Manage Celebrations";
$pageHeader = ($action === 'create') ? "Create New Celebration" : (($action === 'edit') ? "Edit Celebration" : "Celebration Showcase Inventory");
include 'sidebar.php';
?>

<?php if ($action === 'create' || $action === 'edit'): ?>
  <!-- Create / Edit Event Form Card -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid <?= $action === 'edit' ? 'fa-pen-to-square' : 'fa-calendar-plus' ?> me-2" style="color:var(--gold);"></i>
        <?= $action === 'edit' ? 'Edit Celebration: ' . e($editEvent['title']) : 'Register &amp; Curate New Celebration' ?>
      </h3>
      <a href="manage-events.php" class="btn btn-sm btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Inventory
      </a>
    </div>

    <form action="manage-events.php<?= $action === 'edit' ? '?action=edit&id=' . $eventId : '' ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?= $action === 'edit' ? 'update' : 'create' ?>">

      <div class="admin-form-grid">
        <!-- Title -->
        <div class="form-group full-width">
          <label for="title">Celebration Title *</label>
          <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Royal Palace Heritage Wedding &amp; Mandap Showcase" required value="<?= e($editEvent['title'] ?? '') ?>">
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category_id">Royal Category *</label>
          <select id="category_id" name="category_id" class="form-control" required>
            <option value="">Select Royal Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= (isset($editEvent['category_id']) && $editEvent['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= e($cat['category_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label for="status">Celebration Status *</label>
          <select id="status" name="status" class="form-control" required>
            <option value="upcoming" <?= (isset($editEvent['status']) && $editEvent['status'] === 'upcoming') ? 'selected' : '' ?>>Upcoming</option>
            <option value="completed" <?= (isset($editEvent['status']) && $editEvent['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= (isset($editEvent['status']) && $editEvent['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>

        <!-- Venue -->
        <div class="form-group full-width">
          <label for="venue">Royal Venue &amp; Palace Location *</label>
          <input type="text" id="venue" name="venue" class="form-control" placeholder="e.g. The City Palace, Pichola Lake, Udaipur" required value="<?= e($editEvent['venue'] ?? '') ?>">
        </div>

        <!-- Date & Time -->
        <div class="form-group">
          <label for="event_date">Celebration Date *</label>
          <input type="date" id="event_date" name="event_date" class="form-control" required value="<?= e($editEvent['event_date'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="event_time">Celebration Time *</label>
          <input type="time" id="event_time" name="event_time" class="form-control" required value="<?= e($editEvent['event_time'] ?? '') ?>">
        </div>

        <!-- Pricing & Seats -->
        <div class="form-group">
          <label for="ticket_price">Pass Contribution (INR ₹) *</label>
          <input type="number" step="0.01" min="0" id="ticket_price" name="ticket_price" class="form-control" placeholder="0.00" required value="<?= e($editEvent['ticket_price'] ?? '0.00') ?>">
        </div>

        <div class="form-group">
          <label for="total_seats">Total Guest Capacity *</label>
          <input type="number" min="1" id="total_seats" name="total_seats" class="form-control" placeholder="Total available passes" required value="<?= e($editEvent['total_seats'] ?? '100') ?>">
        </div>

        <!-- Description -->
        <div class="form-group full-width">
          <label for="description">Celebration Narrative &amp; Hospitality Inclusions *</label>
          <textarea id="description" name="description" class="form-control" rows="5" placeholder="Detailed overview of royal decor, floral architecture, musical mehfil artists, catering banquets, and guest guidelines..." required><?= e($editEvent['description'] ?? '') ?></textarea>
        </div>

        <!-- Image Upload -->
        <div class="form-group full-width">
          <label for="eventImage">Celebration Banner Image</label>
          <input type="file" id="eventImage" name="image" class="form-control" accept="image/png, image/jpeg, image/webp">
          <div class="image-preview-box">
            <img id="previewImg" src="<?= isset($editEvent['image']) ? '../assets/images/events/' . e($editEvent['image']) : '' ?>" style="<?= isset($editEvent['image']) ? 'display:block;' : 'display:none;' ?>" alt="Preview">
            <span id="previewPlaceholder" style="<?= isset($editEvent['image']) ? 'display:none;' : 'display:block;' ?> color:var(--charcoal-muted); font-size:13px;">
              <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload a royal photography banner (JPG, PNG, WebP)
            </span>
          </div>
        </div>
      </div>

      <div style="padding:0 24px 24px; display:flex; gap:12px;">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check me-1"></i> <?= $action === 'edit' ? 'Save Changes' : 'Publish Celebration' ?>
        </button>
        <a href="manage-events.php" class="btn btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);">Cancel</a>
      </div>
    </form>
  </div>

<?php else: ?>
  <!-- Events List Table -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div style="display:flex; align-items:center; gap:16px;">
        <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
          <i class="fa-solid fa-crown me-2" style="color:var(--gold);"></i> All Registered Celebrations (<?= count($allEvents) ?>)
        </h3>
      </div>
      <div class="admin-toolbar">
        <div class="search-wrapper">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="tableSearch" placeholder="Search celebrations or venue..." class="search-input">
        </div>
        <select id="statusFilter" class="select-filter">
          <option value="">All Statuses</option>
          <option value="upcoming">Upcoming</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <a href="manage-events.php?action=create" class="btn btn-sm btn-primary toolbar-btn">
          <i class="fa-solid fa-plus me-1"></i> New Celebration
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Celebration</th>
            <th>Category</th>
            <th>Schedule</th>
            <th>Pass Price</th>
            <th>Capacity</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allEvents as $ev): ?>
            <?php 
              $bookedSeats = max(0, $ev['total_seats'] - $ev['available_seats']);
              $occupancy = $ev['total_seats'] > 0 ? round(($bookedSeats / $ev['total_seats']) * 100) : 0;
            ?>
            <tr>
              <td>
                <div style="display:flex; align-items:center; gap:14px;">
                  <img src="../assets/images/events/<?= e($ev['image']) ?>" 
                       onerror="this.src='../assets/images/events/default-event.jpg'" 
                       class="table-event-thumb" alt="" style="border:1.5px solid var(--gold-border);">
                  <div>
                    <strong style="color:var(--burgundy); font-size:14.5px; display:block;">
                      <?= e($ev['title']) ?>
                    </strong>
                    <span style="font-size:12px; color:var(--charcoal-muted);">
                      <i class="fa-solid fa-location-dot me-1" style="color:var(--gold);"></i> <?= e(substr($ev['venue'], 0, 30)) ?><?= strlen($ev['venue']) > 30 ? '...' : '' ?>
                    </span>
                  </div>
                </div>
              </td>
              <td class="nowrap">
                <span class="royal-category-pill">
                  <i class="fa-solid fa-gem" style="font-size:10px; color:var(--gold);"></i>
                  <?= e($ev['category_name']) ?>
                </span>
              </td>
              <td class="nowrap">
                <div style="font-size:13px; font-weight:600; color:var(--charcoal);">
                  <i class="fa-regular fa-calendar me-1" style="color:var(--gold);"></i> <?= format_date($ev['event_date']) ?>
                </div>
                <div style="font-size:12px; color:var(--charcoal-muted); margin-top:2px;">
                  <i class="fa-regular fa-clock me-1" style="color:var(--gold);"></i> <?= format_time($ev['event_time']) ?>
                </div>
              </td>
              <td class="nowrap">
                <strong style="color:var(--burgundy); font-size:14.5px;">
                  <?= format_price($ev['ticket_price']) ?>
                </strong>
              </td>
              <td class="nowrap">
                <div style="font-weight:700; font-size:13.5px; color:<?= $ev['available_seats'] < 35 ? 'var(--burgundy)' : 'var(--charcoal)' ?>;">
                  <?= number_format($ev['available_seats']) ?> <span style="font-weight:400; font-size:11.5px; color:var(--charcoal-muted);">left / <?= number_format($ev['total_seats']) ?></span>
                </div>
                <div class="mini-capacity-bar" title="<?= $occupancy ?>% Booked (<?= $bookedSeats ?> reserved)">
                  <div class="mini-capacity-fill" style="width: <?= min(100, $occupancy) ?>%;"></div>
                </div>
                <div style="font-size:11px; color:var(--charcoal-muted); margin-top:3px;"><?= $occupancy ?>% booked</div>
              </td>
              <td>
                <?= get_status_badge($ev['status']) ?>
              </td>
              <td style="text-align:right;" class="nowrap">
                <div class="action-btns" style="justify-content:flex-end;">
                  <a href="../event-details.php?id=<?= $ev['id'] ?>" target="_blank" class="btn-icon view" title="View Public Showcase Page">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                  <a href="manage-events.php?action=edit&id=<?= $ev['id'] ?>" class="btn-icon edit" title="Edit Celebration Details">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a href="manage-events.php?action=delete&id=<?= $ev['id'] ?>" class="btn-icon delete" title="Delete Celebration" onclick="return confirmAction('Are you sure you want to permanently delete this celebration? All associated passes will also be removed.')">
                    <i class="fa-solid fa-trash-can"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
