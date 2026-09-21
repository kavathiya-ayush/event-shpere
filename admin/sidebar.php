<?php
// admin/sidebar.php - Reusable Royal Admin Dashboard Sidebar & Topbar Shell
require_once '../config/db.php';
require_once '../includes/functions.php';

require_admin('index.php');

$currentAdminPage = basename($_SERVER['PHP_SELF']);
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | Bhakti Events Royal Console' : 'Bhakti Events Royal Console' ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<div class="admin-layout">
  
  <!-- Left Sidebar Navigation -->
  <aside class="admin-sidebar">
    <a href="dashboard.php" class="sidebar-brand">
      <div class="brand-icon" style="width:38px; height:38px; font-size:18px; background:linear-gradient(135deg, var(--gold-light), var(--gold)); color:var(--burgundy-dark); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(197,160,89,0.3);">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div style="display:flex; flex-direction:column; line-height:1.2;">
        <span class="brand-title" style="font-family:var(--font-heading); color:#fff; font-size:16px; font-weight:700;">
          BHAKTI <span style="color:var(--gold-light);">EVENTS</span>
        </span>
        <span style="font-size:10px; color:var(--gold); letter-spacing:1px; text-transform:uppercase;">Royal Console</span>
      </div>
    </a>

    <ul class="sidebar-nav">
      <li class="nav-section-title">Operations</li>
      <li>
        <a href="dashboard.php" class="<?= $currentAdminPage === 'dashboard.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-gauge-high"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="manage-events.php" class="<?= $currentAdminPage === 'manage-events.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-calendar-days"></i>
          <span>Manage Events</span>
        </a>
      </li>
      <li>
        <a href="manage-bookings.php" class="<?= $currentAdminPage === 'manage-bookings.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-receipt"></i>
          <span>Guest Bookings</span>
        </a>
      </li>
      <li>
        <a href="manage-categories.php" class="<?= $currentAdminPage === 'manage-categories.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-tags"></i>
          <span>Event Categories</span>
        </a>
      </li>
      <li>
        <a href="manage-users.php" class="<?= $currentAdminPage === 'manage-users.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-users"></i>
          <span>Guest Directory</span>
        </a>
      </li>
      <?php
      $newInquiriesCount = 0;
      try {
          $inqCountStmt = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'New'");
          $newInquiriesCount = $inqCountStmt ? (int)$inqCountStmt->fetchColumn() : 0;
      } catch (Exception $e) {}
      ?>
      <li>
        <a href="manage-inquiries.php" class="<?= $currentAdminPage === 'manage-inquiries.php' ? 'active' : '' ?>" style="display:flex; align-items:center;">
          <i class="fa-solid fa-feather-pointed"></i>
          <span>Client Inquiries</span>
          <?php if ($newInquiriesCount > 0): ?>
            <span style="background:linear-gradient(135deg, #FDE68A 0%, #C5A059 100%); color:#1A0307; font-size:10px; font-weight:800; padding:2px 7px; border-radius:10px; margin-left:auto; box-shadow:0 2px 5px rgba(0,0,0,0.3);">
              <?= $newInquiriesCount ?>
            </span>
          <?php endif; ?>
        </a>
      </li>

      <li class="nav-section-title" style="margin-top:14px;">External</li>
      <li>
        <a href="../index.php" target="_blank">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
          <span>View Public Site</span>
        </a>
      </li>
      <li>
        <a href="logout.php" style="color:#FCA5A5;">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span>Sign Out</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-footer" style="background:rgba(0,0,0,0.25); border-top:1px solid rgba(197,160,89,0.2); padding:16px 20px;">
      <div style="font-weight:700; color:var(--gold-light); font-size:12px; margin-bottom:2px;">Bhakti Events &bull; Gondal</div>
      <div style="font-size:11px; color:#E5D0BA;">Tirumala Mall, Gundala Darwaja</div>
    </div>
  </aside>

  <!-- Right Main Workspace -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-breadcrumb">
        Console / <strong><?= isset($pageHeader) ? e($pageHeader) : 'Dashboard Overview' ?></strong>
      </div>
      <div class="topbar-actions">
        <!-- Live Gujarat Digital Clock from admin.js -->
        <div id="adminClock" style="font-family:monospace; font-weight:700; font-size:13.5px; color:var(--burgundy); background:var(--ivory-soft); padding:6px 14px; border-radius:6px; border:1px solid var(--gold-border);">
          --:--:--
        </div>

        <a href="manage-events.php?action=create" class="btn btn-sm btn-primary">
          <i class="fa-solid fa-plus me-1"></i> New Celebration
        </a>
        <div style="display:flex; align-items:center; gap:10px; border-left:1px solid var(--gold-border); padding-left:16px;">
          <div style="width:36px; height:36px; border-radius:50%; background:var(--burgundy); color:var(--gold-light); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; border:1px solid var(--gold);">
            <i class="fa-solid fa-user-tie"></i>
          </div>
          <div>
            <div style="font-size:13.5px; font-weight:700; color:var(--burgundy-dark);"><?= e($_SESSION['fullname']) ?></div>
            <div style="font-size:11.5px; color:var(--charcoal-muted);">Royal Concierge Director</div>
          </div>
        </div>
      </div>
    </header>

    <?php if ($flash): ?>
      <div style="padding: 20px 30px 0;">
        <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
          <div><i class="fa-solid fa-circle-info me-2"></i> <?= e($flash['message']) ?></div>
          <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
      </div>
    <?php endif; ?>

    <div class="admin-content">
