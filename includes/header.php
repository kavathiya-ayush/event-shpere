<?php
// includes/header.php - Royal Indian Header & Navigation
require_once __DIR__ . '/functions.php';

$isSubfolder = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'user' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin');
$base = $isSubfolder ? '../' : './';

$currentPage = basename($_SERVER['PHP_SELF']);
$flash = get_flash();
$cacheBust = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | Bhakti Events & Celebrations' : 'Bhakti Events | Royal Weddings & Cultural Celebrations' ?></title>
  <meta name="description" content="Premier royal Indian event management, destination wedding planning, traditional mandap decor, and live cultural celebration ticketing in Gujarat.">
  
  <!-- Google Fonts: Playfair Display & Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- FontAwesome 6 Icons CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Core Design System & Stylesheets with Cache Buster -->
  <link rel="stylesheet" href="<?= $base ?>assets/css/style.css?v=<?= $cacheBust ?>">
  <link rel="stylesheet" href="<?= $base ?>assets/css/responsive.css?v=<?= $cacheBust ?>">
</head>
<body>

<!-- Gold Reading & Scroll Progress Bar -->
<div class="scroll-progress-bar" id="scrollProgressBar"></div>

<!-- Top Announcement & Direct Contact Bar (Inspired by Bhakti Creation) -->
<div class="top-bar">
  <div class="container top-bar-inner">
    <div class="top-bar-left">
      <span class="top-bar-badge">
        <i class="fa-solid fa-gem"></i> Flagship Studio
      </span>
      <span class="top-bar-item">
        <i class="fa-solid fa-location-dot"></i>
        Tirumala Shopping Mall, Gundala Darwaja, Gondal
      </span>
      <span class="top-bar-sep">&bull;</span>
      <span class="top-bar-item">
        <i class="fa-regular fa-clock"></i>
        Mon - Sat: 10:00 AM - 8:30 PM
      </span>
    </div>
    <div class="top-bar-right">
      <a href="tel:+919825661046" class="top-bar-item">
        <i class="fa-solid fa-phone"></i> +91 9825661046
      </a>
      <span class="top-bar-sep">&bull;</span>
      <a href="mailto:hp6224974@gmail.com" class="top-bar-item">
        <i class="fa-solid fa-envelope"></i> hp6224974@gmail.com
      </a>
      <span class="top-bar-sep">&bull;</span>
      <a href="https://wa.me/919825661046?text=Namaste%20Bhakti%20Events!%20I%20would%20like%20to%20inquire%20about%20event%20planning%20and%20celebration%20passes." target="_blank" rel="noopener noreferrer" class="top-bar-item top-bar-wa">
        <span class="live-dot"></span>
        <i class="fa-brands fa-whatsapp"></i> WhatsApp Concierge
      </a>
    </div>
  </div>
</div>

<!-- Global Sticky Navigation Bar -->
<header class="site-header main-header" id="siteHeader">
  <div class="nav-container">
    
    <!-- Royal Brand Identity -->
    <a href="<?= $base ?>index.php" class="brand-identity" id="brandIdentityLink">
      <div class="brand-crest">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div class="brand-text">
        <span class="brand-name">BHAKTI <span>EVENTS</span></span>
        <span class="brand-tagline">Royal Celebrations &bull; Est. 2014</span>
      </div>
    </a>

    <!-- Mobile Drawer Toggle -->
    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation" style="display:none; background:transparent; border:none; color:var(--gold-light); font-size:24px; cursor:pointer;">
      <i class="fa-solid fa-bars-staggered"></i>
    </button>

    <!-- Navigation Menu Links (Single Row, No Wrapping) -->
    <nav>
      <ul class="nav-links" id="navLinks">
        <li>
          <a href="<?= $base ?>index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
        </li>
        <li>
          <a href="<?= $base ?>events.php" class="nav-link <?= $currentPage === 'events.php' || $currentPage === 'event-details.php' ? 'active' : '' ?>">Celebrations</a>
        </li>
        <li>
          <a href="<?= $base ?>index.php#destinations" class="nav-link">Destinations</a>
        </li>
        <li>
          <a href="<?= $base ?>index.php#portfolio" class="nav-link">Portfolio</a>
        </li>
        <li>
          <a href="<?= $base ?>index.php#budgetEstimator" class="nav-link">
            <i class="fa-solid fa-calculator" style="color:var(--gold-light); font-size:11px;"></i> Estimator
          </a>
        </li>
        <li>
          <a href="<?= $base ?>contact.php" class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>">Contact</a>
        </li>
      </ul>
    </nav>

    <!-- Header Actions & WhatsApp CTA -->
    <div class="header-actions">
      <a href="https://wa.me/919825661046?text=Namaste%20Bhakti%20Events!%20I%20would%20like%20to%20book%20a%20consultation%20for%20our%20upcoming%20celebration." target="_blank" rel="noopener noreferrer" class="btn-header-wa">
        <i class="fa-brands fa-whatsapp"></i> WhatsApp Us
      </a>

      <?php if (is_logged_in()): ?>
        <div class="user-menu-wrapper" style="position:relative;">
          <button class="user-menu-btn" id="userMenuBtn">
            <span style="width:26px; height:26px; border-radius:50%; background:var(--burgundy-gradient); border:1px solid var(--gold); color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700;">
              <?= strtoupper(substr($_SESSION['fullname'] ?? 'G', 0, 1)) ?>
            </span>
            <span><?= e(explode(' ', $_SESSION['fullname'] ?? 'Guest')[0]) ?></span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px; color:var(--gold);"></i>
          </button>
          
          <div class="user-dropdown" id="userDropdown">
            <div class="user-dropdown-header">
              <span style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:var(--charcoal-muted); display:block;">Signed in as</span>
              <strong style="font-size:14px; color:var(--burgundy); display:block; margin-top:2px; font-family:var(--font-heading);"><?= e($_SESSION['fullname'] ?? 'Guest') ?></strong>
              <span class="badge-pill" style="font-size:10px; padding:2px 8px; margin-top:4px; display:inline-block; background:<?= is_admin() ? 'var(--burgundy)' : 'var(--ivory-soft)' ?>; color:<?= is_admin() ? 'var(--gold-light)' : 'var(--burgundy)' ?>; border:1px solid var(--gold-border);">
                <?= is_admin() ? '👑 Royal Admin' : '🎟️ Member Patron' ?>
              </span>
            </div>
            
            <?php if (is_admin()): ?>
              <a href="<?= $base ?>admin/dashboard.php" class="user-dropdown-item admin-badge-item">
                <i class="fa-solid fa-crown" style="color:var(--gold);"></i> Admin Royal Console
              </a>
            <?php else: ?>
              <a href="<?= $base ?>login.php?switch=admin" class="user-dropdown-item" style="background:rgba(197,160,89,0.08); font-weight:600;">
                <i class="fa-solid fa-arrow-right-arrow-left" style="color:var(--gold);"></i> Switch to Admin
              </a>
            <?php endif; ?>
            
            <a href="<?= $base ?>user/my-bookings.php" class="user-dropdown-item">
              <i class="fa-solid fa-ticket"></i> My Royal Passes
            </a>
            <a href="<?= $base ?>user/profile.php" class="user-dropdown-item">
              <i class="fa-solid fa-user-gear"></i> Account Profile
            </a>
            
            <div style="height:1px; background:var(--gold-border); margin:4px 0;"></div>
            
            <a href="<?= $base ?>logout.php" class="user-dropdown-item danger-item">
              <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= $base ?>login.php" class="btn-header-signin">
          <i class="fa-regular fa-circle-user"></i> Sign In
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Flash Alerts Notification System -->
<?php if ($flash): ?>
  <div class="container" style="margin-top:16px;">
    <div class="alert alert-<?= e($flash['type']) ?>" style="background:#FFFDF9; border:1.5px solid <?= $flash['type'] === 'success' ? '#10B981' : ($flash['type'] === 'danger' ? '#EF4444' : '#C5A059') ?>; border-radius:8px; padding:12px 18px; display:flex; justify-content:space-between; align-items:center; box-shadow:var(--shadow-sm);">
      <div style="font-size:14px; font-weight:500; color:var(--primary);">
        <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check text-success' : 'fa-circle-info text-gold' ?> me-2"></i>
        <?= e($flash['message']) ?>
      </div>
      <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; font-size:18px; cursor:pointer; color:var(--text-muted);">&times;</button>
    </div>
  </div>
<?php endif; ?>

<main>
