<?php
// events.php - Browse All Royal Events with Search, Category Filter & Sorting
require_once 'config/db.php';
$pageTitle = "Royal Celebrations & Event Showcase";
include 'includes/header.php';

// Fetch all categories for filter buttons
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

// Process query parameters
$search   = isset($_GET['q']) ? trim($_GET['q']) : '';
$catId    = isset($_GET['category']) ? intval($_GET['category']) : 0;
$sort     = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_asc';

// Build dynamic WHERE clause
$whereClauses = ["e.status = 'upcoming'"];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ?)";
    $term = "%" . $search . "%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

if ($catId > 0) {
    $whereClauses[] = "e.category_id = ?";
    $params[] = $catId;
}

// Build dynamic ORDER BY clause
$orderBy = "e.event_date ASC, e.event_time ASC";
if ($sort === 'price_low') {
    $orderBy = "e.ticket_price ASC";
} elseif ($sort === 'price_high') {
    $orderBy = "e.ticket_price DESC";
} elseif ($sort === 'newest') {
    $orderBy = "e.created_at DESC";
}

$whereSql = implode(' AND ', $whereClauses);
$sql = "SELECT e.*, c.category_name 
        FROM events e 
        JOIN categories c ON e.category_id = c.id 
        WHERE $whereSql 
        ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();
?>

<!-- Header Banner -->
<div style="background:linear-gradient(135deg, var(--burgundy-dark) 0%, var(--burgundy) 100%); color:#fff; padding:55px 20px; border-bottom:2px solid var(--gold);">
  <div class="container">
    <div style="max-width:760px;">
      <span class="badge-pill" style="background:rgba(197,160,89,0.2); color:var(--gold-light); border-color:var(--gold); margin-bottom:12px;">
        <i class="fa-solid fa-crown"></i> Curated Celebrations &bull; Gujarat &amp; Beyond
      </span>
      <h1 style="color:var(--gold-light); font-size:38px; margin-bottom:10px;">Royal Celebrations &amp; Event Showcase</h1>
      <p style="color:#F5EBE6; font-size:16px; line-height:1.6;">Browse premier palace weddings, soulful Sufi mehfils, cultural Navratri Garba, and corporate leadership banquets. Reserve guaranteed passes or inquire for bespoke VIP arrangements.</p>
    </div>
  </div>
</div>

<div class="container" style="padding: 40px 20px 80px;">
  <!-- Filter & Search Controls Bar -->
  <div style="background:#fff; border-radius:14px; border:1px solid var(--gold-border); padding:22px; box-shadow:var(--shadow-sm); margin-bottom:35px;">
    <form action="events.php" method="GET" style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between;">
      
      <!-- Search Input -->
      <div style="flex:1; min-width:260px; display:flex; align-items:center; border:1px solid var(--gold-border); border-radius:8px; padding:6px 14px; background:var(--ivory-soft);">
        <i class="fa-solid fa-magnifying-glass" style="color:var(--gold); margin-right:10px;"></i>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search celebrations, artists, venues..." style="width:100%; border:none; outline:none; background:transparent; font-family:inherit; font-size:14px; padding:4px 0; color:var(--charcoal);">
      </div>

      <!-- Category Filter -->
      <div style="min-width:190px;">
        <select name="category" class="form-control" style="padding:10px 12px; font-size:14px;" onchange="this.form.submit()">
          <option value="">All Royal Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $catId === intval($cat['id']) ? 'selected' : '' ?>>
              <?= e($cat['category_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Sort Options -->
      <div style="min-width:190px;">
        <select name="sort" class="form-control" style="padding:10px 12px; font-size:14px;" onchange="this.form.submit()">
          <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Date: Upcoming First</option>
          <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Recently Added</option>
        </select>
      </div>

      <!-- Submit & Reset Buttons -->
      <div style="display:flex; gap:10px;">
        <button type="submit" class="btn btn-sm btn-primary" style="padding:10px 18px;">
          <i class="fa-solid fa-filter"></i> Apply
        </button>
        <?php if (!empty($search) || $catId > 0 || $sort !== 'date_asc'): ?>
          <a href="events.php" class="btn btn-sm btn-outline-primary" style="padding:10px 14px;" title="Clear all filters">
            <i class="fa-solid fa-rotate-left"></i> Reset
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Active Results Counter -->
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <p style="color:var(--charcoal-muted); font-size:15px; margin:0;">
      Showing <strong><?= count($events) ?></strong> <?= count($events) === 1 ? 'celebration' : 'celebrations' ?> available
      <?php if (!empty($search)): ?> matching "<strong><?= e($search) ?></strong>"<?php endif; ?>
    </p>
  </div>

  <!-- Event Grid -->
  <div class="events-grid">
    <?php if (!empty($events)): ?>
      <?php foreach ($events as $event): ?>
        <?php 
          $eventDate = new DateTime($event['event_date']);
          $day = $eventDate->format('d');
          $month = $eventDate->format('M');
          $waText = "Hello Bhakti Events, I am interested in booking passes for '{$event['title']}' on {$eventDate->format('d M, Y')}.";
        ?>
        <article class="event-card">
          <div class="card-img-holder">
            <div class="date-badge">
              <span class="date-day"><?= $day ?></span>
              <span class="date-month"><?= $month ?></span>
            </div>

            <span class="category-badge">
              <i class="fa-solid fa-crown me-1" style="font-size:10px;"></i>
              <?= e($event['category_name']) ?>
            </span>

            <img src="assets/images/events/<?= e($event['image']) ?>" 
                 onerror="this.src='assets/images/events/default-event.jpg'" 
                 alt="<?= e($event['title']) ?>"
                 loading="lazy">
          </div>

          <div class="card-body">
            <div class="event-meta">
              <span><i class="fa-regular fa-clock" style="color:var(--burgundy);"></i> <?= format_time($event['event_time']) ?></span>
              <span><i class="fa-solid fa-location-dot" style="color:var(--burgundy);"></i> <?= e(substr($event['venue'], 0, 24)) ?><?= strlen($event['venue']) > 24 ? '...' : '' ?></span>
            </div>

            <h3>
              <a href="event-details.php?id=<?= $event['id'] ?>">
                <?= e($event['title']) ?>
              </a>
            </h3>

            <p><?= e(substr($event['description'], 0, 115)) ?>...</p>

            <div class="card-footer">
              <div class="price-box">
                <span class="price-label">Entry Pass From</span>
                <span class="price-value"><?= format_price($event['ticket_price']) ?></span>
              </div>
              
              <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
                <span class="seat-status <?= $event['available_seats'] < 35 ? 'low' : '' ?>">
                  <i class="fa-solid fa-chair" style="font-size:11px;"></i> <?= $event['available_seats'] ?> passes left
                </span>
                <div style="display:flex; gap:6px;">
                  <a href="https://wa.me/919825661046?text=<?= urlencode($waText) ?>" 
                     target="_blank" 
                     rel="noopener" 
                     class="btn btn-sm btn-outline-primary" 
                     title="Inquire via WhatsApp"
                     style="padding:6px 10px; border-color:var(--gold); color:var(--burgundy);">
                    <i class="fa-brands fa-whatsapp" style="color:#25D366; font-size:14px;"></i>
                  </a>
                  <a href="event-details.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-primary">
                    Book Passes
                  </a>
                </div>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align:center; padding:60px 20px; background:#fff; border-radius:16px; border:1px dashed var(--gold-border);">
        <div style="width:64px; height:64px; border-radius:50%; background:var(--ivory-soft); color:var(--burgundy); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:26px; border:1px solid var(--gold-border);">
          <i class="fa-solid fa-calendar-xmark"></i>
        </div>
        <h3 style="font-size:22px; margin-bottom:8px; font-family:var(--font-heading); color:var(--burgundy);">No Matching Celebrations Found</h3>
        <p style="color:var(--charcoal-muted); max-width:460px; margin:0 auto 20px;">Try adjusting your keyword search, selecting another category, or contact our concierge directly.</p>
        <a href="events.php" class="btn btn-primary">
          <i class="fa-solid fa-rotate-left"></i> View All Celebrations
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
