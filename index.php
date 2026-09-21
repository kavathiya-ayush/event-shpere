<?php
// index.php - Bhakti Events & Celebrations Homepage & Royal Showcase
require_once 'config/db.php';
$pageTitle = "Royal Indian Celebrations, Weddings & Luxury Event Management";
include 'includes/header.php';

// Fetch all active categories for quick filters
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $catStmt->fetchAll();

// Fetch top upcoming highlight events
$stmt = $pdo->query("SELECT e.*, c.category_name 
                     FROM events e 
                     JOIN categories c ON e.category_id = c.id 
                     WHERE e.status = 'upcoming' 
                     ORDER BY e.event_date ASC LIMIT 6");
$events = $stmt->fetchAll();

// Platform statistics
$totalEventsCount = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'upcoming'")->fetchColumn();
$totalBookingsCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
?>

<!-- Luxury Hero Section -->
<section class="hero-section">
  <div class="hero-container">
    <span class="badge-pill">
      <i class="fa-solid fa-crown" style="color:var(--gold-light);"></i> Gujarat's Premier Royal Event Curators &bull; Est. 2014
    </span>
    <h1>Crafting Unforgettable Royal Indian Celebrations &amp; Experiences</h1>
    <p>From opulent palace destination weddings and soulful candlelit Sufi nights to grand Navratri Garba mahotsavs and high-profile leadership galas. Experience bespoke heritage hospitality and flawless production.</p>
    
    <!-- Hero CTA Action Group -->
    <div style="display:flex; justify-content:center; gap:16px; margin-bottom:34px; flex-wrap:wrap;">
      <a href="#signatureEvents" class="btn btn-primary" style="padding:14px 30px; font-size:15px;">
        <i class="fa-solid fa-compass me-1"></i> Explore Royal Events
      </a>
      <a href="https://wa.me/919825661046?text=<?= urlencode('Hello Bhakti Events, I would like to inquire about planning an upcoming celebration / wedding.') ?>" 
         target="_blank" 
         rel="noopener" 
         class="btn btn-outline-primary" 
         style="padding:14px 28px; font-size:15px; border-color:var(--gold); color:var(--gold-light);">
        <i class="fa-brands fa-whatsapp me-1" style="color:#25D366;"></i> WhatsApp Concierge
      </a>
    </div>

    <!-- Quick Search Box -->
    <form action="events.php" method="GET" class="search-bar-box" id="heroSearchForm">
      <div class="search-input-group">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" placeholder="Search by royal event, artist, or venue..." aria-label="Search events">
        
        <select name="category" class="search-category-select" aria-label="Select Category">
          <option value="">All Royal Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= e($cat['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="white-space:nowrap; padding:12px 24px;">Find Celebrations</button>
    </form>

    <!-- Platform Heritage Stats Row with Animated Count-Up -->
    <div class="hero-stats-row">
      <div class="stat-item">
        <span class="stat-num">10+</span>
        <span class="stat-label">Years of Heritage</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">500+</span>
        <span class="stat-label">Royal Celebrations</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">25,000+</span>
        <span class="stat-label">Delighted Guests</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">100%</span>
        <span class="stat-label">Verified E-Passes</span>
      </div>
    </div>
  </div>

  <!-- Animated Scroll Down Arrow -->
  <a href="#signatureEvents" id="scrollDownIndicator" style="position:absolute; bottom:25px; left:50%; transform:translateX(-50%); color:var(--gold-light); font-size:22px; text-decoration:none; animation:bounce 2s infinite; z-index:5;">
    <i class="fa-solid fa-chevron-down"></i>
  </a>
</section>

<!-- Upcoming Highlights Section -->
<section class="events-section" id="signatureEvents">
  <div class="container">
    <div class="section-header" style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:32px; text-align:left; flex-wrap:wrap; gap:20px;">
      <div class="section-title" style="max-width:720px; margin-bottom:0;">
        <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
          Curated Experiences
        </span>
        <h2 style="font-size:32px; color:var(--burgundy); margin-bottom:8px;">Signature Royal Celebrations &amp; Passes</h2>
        <div class="gold-divider" style="margin:10px 0 14px 0; justify-content:flex-start;"></div>
        <p style="color:var(--charcoal-muted); font-size:14.5px; margin:0;">Hand-picked heritage destination weddings, musical mehfils, cultural festivals, and corporate galas scheduled this season across Gujarat and Rajasthan.</p>
      </div>
      <div>
        <a href="events.php" class="btn btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy); font-weight:600; white-space:nowrap;">
          View All Celebrations <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>

    <!-- Quick Category Filter Pills -->
    <div style="display:flex; gap:10px; justify-content:flex-start; flex-wrap:wrap; margin-bottom:34px;">
      <a href="events.php" class="filter-btn active" style="font-size:13px; padding:7px 18px;">All Celebrations</a>
      <a href="events.php?category=1" class="filter-btn" style="font-size:13px; padding:7px 18px;">Royal Weddings</a>
      <a href="events.php?category=2" class="filter-btn" style="font-size:13px; padding:7px 18px;">Sufi &amp; Classical</a>
      <a href="events.php?category=3" class="filter-btn" style="font-size:13px; padding:7px 18px;">Garba Mahotsav</a>
      <a href="events.php?category=4" class="filter-btn" style="font-size:13px; padding:7px 18px;">Sangeet &amp; Stage</a>
      <a href="events.php?category=5" class="filter-btn" style="font-size:13px; padding:7px 18px;">Corporate Galas</a>
    </div>

    <div class="events-grid">
      <?php if (!empty($events)): ?>
        <?php foreach ($events as $event): ?>
          <?php 
            $eventDate = new DateTime($event['event_date']);
            $day = $eventDate->format('d');
            $month = $eventDate->format('M');
            $waText = "Hello Bhakti Events, I am interested in attending '{$event['title']}' scheduled on {$eventDate->format('d M, Y')}. Could you share more details?";
          ?>
          <article class="event-card">
            <div class="card-img-holder">
              <!-- Pinned Date Badge -->
              <div class="date-badge">
                <span class="date-day"><?= $day ?></span>
                <span class="date-month"><?= $month ?></span>
              </div>

              <!-- Category Badge -->
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
                <span><i class="fa-solid fa-location-dot" style="color:var(--burgundy);"></i> <?= e(substr($event['venue'], 0, 26)) ?><?= strlen($event['venue']) > 26 ? '...' : '' ?></span>
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
        <div style="grid-column: 1 / -1; text-align:center; padding:60px 20px; background:#fff; border-radius:12px; border:1px dashed var(--gold-border);">
          <i class="fa-regular fa-calendar-xmark" style="font-size:44px; color:var(--gold); margin-bottom:14px;"></i>
          <h3>No Upcoming Celebrations Scheduled</h3>
          <p style="color:var(--charcoal-muted);">Please contact our Gondal office directly for private event inquiries or custom bookings.</p>
          <a href="contact.php" class="btn btn-primary" style="margin-top:16px;">Contact Royal Office</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Interactive Royal Celebration Budget Estimator -->
<section class="estimator-section" id="budgetEstimator">
  <div class="container">
    <div class="section-title" style="text-align:center; max-width:760px; margin:0 auto 40px auto;">
      <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
        Transparent Royal Planning
      </span>
      <h2>Interactive Celebration Budget Estimator</h2>
      <div class="gold-divider"></div>
      <p>Configure your dream celebration parameters below. Get an instant, realistic package estimate and connect directly with our chief celebration director on WhatsApp.</p>
    </div>

    <div class="estimator-card" style="background:#FFFFFF; border:1.5px solid var(--gold-border); border-radius:20px; box-shadow:0 20px 50px rgba(122, 28, 46, 0.08); padding:40px; display:grid; grid-template-columns:1.35fr 1fr; gap:40px;">
      
      <!-- Left Controls Area -->
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
          <div style="width:36px; height:36px; border-radius:50%; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:16px;">
            <i class="fa-solid fa-sliders"></i>
          </div>
          <h3 style="font-family:var(--font-heading); font-size:24px; color:var(--burgundy); margin:0;">
            Customize Celebration Parameters
          </h3>
        </div>
        <p style="color:var(--charcoal-muted); font-size:13.5px; margin-bottom:22px;">Adjust guest count and luxury preferences to see live cost projections.</p>

        <!-- Guest Count Slider Card with Preset Chips -->
        <div class="estimator-group" style="background:var(--ivory-soft); border:1px solid var(--gold-border); border-radius:14px; padding:18px 20px; margin-bottom:20px;">
          <div class="estimator-label" style="display:flex; justify-content:space-between; align-items:center; font-weight:600; font-size:14px; color:var(--charcoal); margin-bottom:10px;">
            <span style="display:flex; align-items:center; gap:8px;">
              <i class="fa-solid fa-users" style="color:var(--burgundy);"></i> Estimated Guest Attendance:
            </span>
            <span id="estGuestsVal" style="color:var(--burgundy); font-weight:800; font-size:16px; background:#fff; padding:4px 14px; border-radius:20px; border:1px solid var(--gold-border); box-shadow:0 2px 8px rgba(0,0,0,0.05);">
              350 Guests
            </span>
          </div>
          <div class="range-slider-wrapper">
            <input type="range" id="estGuests" class="range-slider" min="100" max="1500" step="50" value="350">
          </div>
          <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--charcoal-muted); margin-top:6px;">
            <span>Intimate (100 Guests)</span>
            <span>Grand Royal (1,500+ Guests)</span>
          </div>

          <!-- Quick Preset Chips -->
          <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap; border-top:1px dashed rgba(197,160,89,0.3); padding-top:10px;">
            <span style="font-size:11px; color:var(--charcoal-muted); align-self:center; font-weight:600;">Quick Presets:</span>
            <button type="button" class="guest-chip" data-guests="150" style="background:#fff; border:1px solid var(--gold-border); border-radius:14px; padding:3px 12px; font-size:11.5px; color:var(--burgundy); cursor:pointer; font-weight:600; transition:all 0.2s;">150 Guests</button>
            <button type="button" class="guest-chip" data-guests="350" style="background:#fff; border:1px solid var(--gold-border); border-radius:14px; padding:3px 12px; font-size:11.5px; color:var(--burgundy); cursor:pointer; font-weight:600; transition:all 0.2s;">350 Guests</button>
            <button type="button" class="guest-chip" data-guests="600" style="background:#fff; border:1px solid var(--gold-border); border-radius:14px; padding:3px 12px; font-size:11.5px; color:var(--burgundy); cursor:pointer; font-weight:600; transition:all 0.2s;">600 Guests</button>
            <button type="button" class="guest-chip" data-guests="1000" style="background:#fff; border:1px solid var(--gold-border); border-radius:14px; padding:3px 12px; font-size:11.5px; color:var(--burgundy); cursor:pointer; font-weight:600; transition:all 0.2s;">1,000+ Guests</button>
          </div>
        </div>

        <!-- Event Type Select -->
        <div class="estimator-group" style="margin-bottom:18px;">
          <label class="estimator-label" for="estType" style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13.5px; color:var(--charcoal); margin-bottom:6px;">
            <i class="fa-solid fa-crown" style="color:var(--gold);"></i> Celebration Type
          </label>
          <select id="estType" class="estimator-select">
            <option value="600000">Royal Palace Destination Wedding (3 Days)</option>
            <option value="350000">Royal Sangeet &amp; Choreography Gala</option>
            <option value="250000">Soulful Sufi &amp; Classical Ghazal Mehfil</option>
            <option value="450000">Grand Navratri Cultural Garba Mahotsav</option>
            <option value="400000">Corporate Leadership Banquet &amp; Awards</option>
          </select>
        </div>

        <!-- Venue Tier Select -->
        <div class="estimator-group" style="margin-bottom:18px;">
          <label class="estimator-label" for="estVenue" style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13.5px; color:var(--charcoal); margin-bottom:6px;">
            <i class="fa-solid fa-archway" style="color:var(--gold);"></i> Venue Tier &amp; Location
          </label>
          <select id="estVenue" class="estimator-select">
            <option value="450000">Heritage Lake Palace (e.g. Udaipur / Jaipur)</option>
            <option value="300000">Royal Riverside Heritage Grounds (Gondal)</option>
            <option value="250000">Riverfront Cultural Amphitheater (Ahmedabad)</option>
            <option value="200000">Luxury 5-Star Hotel Ballroom</option>
          </select>
        </div>

        <!-- Mandap & Decor Select -->
        <div class="estimator-group" style="margin-bottom:18px;">
          <label class="estimator-label" for="estDecor" style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13.5px; color:var(--charcoal); margin-bottom:6px;">
            <i class="fa-solid fa-wand-magic-sparkles" style="color:var(--gold);"></i> Mandap &amp; Floral Architecture
          </label>
          <select id="estDecor" class="estimator-select">
            <option value="450000">Shahi Rajputana Carved Wooden Arches &amp; Dutch Blooms</option>
            <option value="350000">Vedic Sacred Floral Mandap with Fragrant Mogra</option>
            <option value="280000">Contemporary Crystal &amp; Velvet Elegance</option>
            <option value="200000">Traditional Minimalist Marigold Theme</option>
          </select>
        </div>

        <!-- Catering Select -->
        <div class="estimator-group" style="margin-bottom:18px;">
          <label class="estimator-label" for="estCatering" style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13.5px; color:var(--charcoal); margin-bottom:6px;">
            <i class="fa-solid fa-utensils" style="color:var(--gold);"></i> Catering Style (Per Plate)
          </label>
          <select id="estCatering" class="estimator-select">
            <option value="1500">Royal Rajwadi Kathiyawadi &amp; Awadhi Feast (₹1,500/plate)</option>
            <option value="1200">Authentic Shahi Gujarati Thal &amp; Live Chaat (₹1,200/plate)</option>
            <option value="1800">Multi-Cuisine Continental &amp; Pan-Asian Fusion (₹1,800/plate)</option>
            <option value="900">High-Tea &amp; Artisan Street Delicacies (₹900/plate)</option>
          </select>
        </div>

        <!-- Artist Select -->
        <div class="estimator-group" style="margin-bottom:10px;">
          <label class="estimator-label" for="estArtist" style="display:flex; align-items:center; gap:8px; font-weight:600; font-size:13.5px; color:var(--charcoal); margin-bottom:6px;">
            <i class="fa-solid fa-music" style="color:var(--gold);"></i> Entertainment &amp; Celebrity Artists
          </label>
          <select id="estArtist" class="estimator-select">
            <option value="300000">Renowned Sufi Qawwals &amp; Classical Sitar Virtuosos</option>
            <option value="450000">Celebrity Bollywood Singer &amp; Symphony Band</option>
            <option value="200000">Traditional Folk Dhol Tasha &amp; Garba Vocalists</option>
            <option value="150000">Intelligent Kinetic DJ &amp; Laser Production</option>
          </select>
        </div>
      </div>

      <!-- Right Receipt Display (Explicit Royal Burgundy Card with High-Contrast Typography) -->
      <div class="estimator-receipt" style="background:linear-gradient(145deg, #2D050B 0%, #4E0A16 50%, #6E1220 100%) !important; color:#FFFFFF !important; border:2px solid #C5A059 !important; border-radius:20px !important; box-shadow:0 25px 60px rgba(45, 5, 11, 0.45) !important; padding:34px !important; display:flex !important; flex-direction:column !important; justify-content:space-between !important; position:relative !important; overflow:hidden !important;">
        
        <!-- Watermark Crest -->
        <div style="position:absolute; right:-20px; top:-20px; font-size:180px; color:rgba(197,160,89,0.06); pointer-events:none;">
          <i class="fa-solid fa-crown"></i>
        </div>

        <div style="position:relative; z-index:2;">
          <div class="receipt-header" style="border-bottom:1px solid rgba(197,160,89,0.3); padding-bottom:16px; margin-bottom:18px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
              <i class="fa-solid fa-crown" style="color:#FDE68A; font-size:18px;"></i>
              <h4 style="font-family:var(--font-heading); color:#FDE68A; font-size:22px; margin:0;">
                Bespoke Royal Package Summary
              </h4>
            </div>
            <p style="color:#F5EBE6; font-size:13px; margin:4px 0 0;">Custom turnkey estimate for your royal gathering</p>
          </div>

          <!-- Itemized Breakdown Rows -->
          <div class="receipt-breakdown" style="display:flex; flex-direction:column; gap:14px; margin-bottom:22px;">
            <div class="receipt-row" style="display:flex; justify-content:space-between; align-items:center; font-size:13.5px; color:#F5EBE6; padding-bottom:10px; border-bottom:1px dashed rgba(197,160,89,0.3);">
              <span style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-archway" style="color:#FDE68A; font-size:12px;"></i> Venue &amp; Directorship:
              </span>
              <span id="receiptVenue" style="color:#FDE68A; font-weight:700; font-family:var(--font-heading); font-size:16px;">₹4,50,000</span>
            </div>

            <div class="receipt-row" style="display:flex; justify-content:space-between; align-items:center; font-size:13.5px; color:#F5EBE6; padding-bottom:10px; border-bottom:1px dashed rgba(197,160,89,0.3);">
              <span style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-wand-magic-sparkles" style="color:#FDE68A; font-size:12px;"></i> Mandap &amp; Floral Decor:
              </span>
              <span id="receiptDecor" style="color:#FDE68A; font-weight:700; font-family:var(--font-heading); font-size:16px;">₹6,90,000</span>
            </div>

            <div class="receipt-row" style="display:flex; justify-content:space-between; align-items:center; font-size:13.5px; color:#F5EBE6; padding-bottom:10px; border-bottom:1px dashed rgba(197,160,89,0.3);">
              <span style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-utensils" style="color:#FDE68A; font-size:12px;"></i> Shahi Gourmet Catering:
              </span>
              <span id="receiptCatering" style="color:#FDE68A; font-weight:700; font-family:var(--font-heading); font-size:16px;">₹5,25,000</span>
            </div>

            <div class="receipt-row" style="display:flex; justify-content:space-between; align-items:center; font-size:13.5px; color:#F5EBE6; padding-bottom:10px; border-bottom:1px dashed rgba(197,160,89,0.3);">
              <span style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-music" style="color:#FDE68A; font-size:12px;"></i> Live Artists &amp; Audio SFX:
              </span>
              <span id="receiptArtist" style="color:#FDE68A; font-weight:700; font-family:var(--font-heading); font-size:16px;">₹3,00,000</span>
            </div>
          </div>
        </div>

        <div style="position:relative; z-index:2;">
          <!-- Total Box -->
          <div class="receipt-total" style="background:rgba(0,0,0,0.45); border:1.5px solid #C5A059; border-radius:14px; padding:18px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <span style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; color:#E5D0BA; display:block; margin-bottom:2px; font-weight:700;">
                Estimated Investment
              </span>
              <span style="font-size:12px; color:#CBD5E1;">Turnkey Royal Execution</span>
            </div>
            <div class="value" id="receiptTotalVal" style="font-size:32px; font-family:var(--font-heading); font-weight:800; color:#FDE68A; text-shadow:0 0 15px rgba(253, 230, 138, 0.4);">
              ₹19,65,000
            </div>
          </div>

          <a href="#" id="estimatorWaBtn" target="_blank" rel="noopener" class="btn" style="width:100%; text-align:center; padding:16px; font-size:15px; font-weight:700; display:flex; align-items:center; justify-content:center; gap:10px; background:linear-gradient(135deg, #25D366 0%, #128C7E 100%) !important; color:#FFFFFF !important; border-radius:12px; box-shadow:0 10px 25px rgba(37, 211, 102, 0.35); border:1px solid rgba(255,255,255,0.4);">
            <i class="fa-brands fa-whatsapp" style="font-size:20px; color:#FFF;"></i> Inquire This Package on WhatsApp
          </a>

          <div style="text-align:center; margin-top:12px;">
            <a href="tel:+919825661046" style="color:#FDE68A; font-size:12px; text-decoration:none;">
              <i class="fa-solid fa-phone me-1"></i> Speak directly with our celebration director: +91 9825661046
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Royal Destinations & Palace Showcase -->
<section class="destinations-section" id="destinations">
  <div class="container">
    <div class="section-title" style="text-align:center; max-width:720px; margin:0 auto;">
      <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
        Iconic Venues
      </span>
      <h2>Royal Destinations &amp; Palace Venues</h2>
      <div class="gold-divider"></div>
      <p>From historic Mewar palaces in Rajasthan to regal riverside estates in Saurashtra, explore premier estates where we bring celebrations to life.</p>
    </div>

    <div class="destinations-grid">
      <!-- Destination 1: Udaipur -->
      <div class="destination-card">
        <img src="assets/images/events/wedding.jpg" alt="Udaipur Lake Palaces">
        <div class="dest-gradient">
          <span class="dest-badge">Mewar Heritage</span>
          <h3>Udaipur, Rajasthan</h3>
          <p>Jagmandir Island Palace &amp; Lake Pichola waterfront lawns for regal destination weddings.</p>
          <div class="dest-specs">
            <span><i class="fa-solid fa-users me-1"></i> Up to 1,200 Guests</span>
            <span><i class="fa-solid fa-hotel me-1"></i> 5-Star Heritage</span>
          </div>
        </div>
      </div>

      <!-- Destination 2: Gondal -->
      <div class="destination-card">
        <img src="assets/images/events/garba.jpg" alt="Gondal Heritage Palaces">
        <div class="dest-gradient">
          <span class="dest-badge">Saurashtra Royal Estate</span>
          <h3>Gondal, Gujarat</h3>
          <p>Riverside Palace grounds and historic arches tailored for authentic Garba &amp; wedding celebrations.</p>
          <div class="dest-specs">
            <span><i class="fa-solid fa-users me-1"></i> Up to 2,500 Guests</span>
            <span><i class="fa-solid fa-gem me-1"></i> Royal Orchards</span>
          </div>
        </div>
      </div>

      <!-- Destination 3: Ahmedabad -->
      <div class="destination-card">
        <img src="assets/images/events/sufi.jpg" alt="Ahmedabad Riverfront">
        <div class="dest-gradient">
          <span class="dest-badge">Cultural Capital</span>
          <h3>Ahmedabad Riverfront</h3>
          <p>Open-air cultural amphitheaters, riverside promenades, and serene twilight musical mehfils.</p>
          <div class="dest-specs">
            <span><i class="fa-solid fa-users me-1"></i> Up to 800 Guests</span>
            <span><i class="fa-solid fa-music me-1"></i> Acoustic Open-Air</span>
          </div>
        </div>
      </div>

      <!-- Destination 4: Gandhinagar / Jaipur -->
      <div class="destination-card">
        <img src="assets/images/events/sangeet.jpg" alt="The Grand Imperial Ballroom">
        <div class="dest-gradient">
          <span class="dest-badge">Luxury Banqueting</span>
          <h3>Grand Imperial Ballrooms</h3>
          <p>High-capacity illuminated ballrooms with kinetic LED lighting, laser staging, and shahi dining.</p>
          <div class="dest-specs">
            <span><i class="fa-solid fa-users me-1"></i> Up to 1,000 Guests</span>
            <span><i class="fa-solid fa-bolt me-1"></i> Concert-Grade AV</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Curated Royal Portfolio Gallery -->
<section class="portfolio-section" id="portfolio">
  <div class="container">
    <div class="section-title" style="text-align:center; max-width:720px; margin:0 auto;">
      <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
        Visual Grandeur
      </span>
      <h2>Curated Royal Portfolio</h2>
      <div class="gold-divider"></div>
      <p>Browse signature installations executed across Gujarat and Rajasthan over the last decade.</p>
    </div>

    <!-- Filter Buttons -->
    <div class="portfolio-filter-bar">
      <button type="button" class="filter-btn active" data-filter="all">All Portfolio</button>
      <button type="button" class="filter-btn" data-filter="mandap">Mandap &amp; Floral</button>
      <button type="button" class="filter-btn" data-filter="sangeet">Sangeet &amp; Stage</button>
      <button type="button" class="filter-btn" data-filter="sufi">Sufi &amp; Mehfil</button>
      <button type="button" class="filter-btn" data-filter="garba">Garba Mahotsav</button>
      <button type="button" class="filter-btn" data-filter="gala">Corporate Galas</button>
    </div>

    <!-- Portfolio Grid -->
    <div class="portfolio-grid">
      <div class="portfolio-card" data-category="mandap">
        <img src="assets/images/events/wedding.jpg" alt="Vedic Royal Mandap" loading="lazy">
        <div class="portfolio-overlay">
          <h4>Vedic Floral Mandap</h4>
          <span>Lake Pichola Estate &bull; Udaipur</span>
        </div>
      </div>

      <div class="portfolio-card" data-category="sufi">
        <img src="assets/images/events/sufi.jpg" alt="Sufi Night Candlelight" loading="lazy">
        <div class="portfolio-overlay">
          <h4>Twilight Candlelit Mehfil</h4>
          <span>Riverfront Amphitheater &bull; Ahmedabad</span>
        </div>
      </div>

      <div class="portfolio-card" data-category="garba">
        <img src="assets/images/events/garba.jpg" alt="Navratri Rasleela" loading="lazy">
        <div class="portfolio-overlay">
          <h4>Heritage Raas-Garba Mahotsav</h4>
          <span>Heritage Palace Grounds &bull; Gondal</span>
        </div>
      </div>

      <div class="portfolio-card" data-category="sangeet">
        <img src="assets/images/events/sangeet.jpg" alt="Bollywood Symphony Gala" loading="lazy">
        <div class="portfolio-overlay">
          <h4>Symphony Sangeet Stage</h4>
          <span>The Grand Ballroom &bull; Gandhinagar</span>
        </div>
      </div>

      <div class="portfolio-card" data-category="gala">
        <img src="assets/images/events/gala.jpg" alt="Leadership Banquet" loading="lazy">
        <div class="portfolio-overlay">
          <h4>Corporate Leadership Banquet</h4>
          <span>Convention Ballroom &bull; Mumbai</span>
        </div>
      </div>

      <div class="portfolio-card" data-category="mandap">
        <img src="assets/images/events/youth-conclave.jpg" alt="Conclave Lighting" loading="lazy">
        <div class="portfolio-overlay">
          <h4>Grand Lighting Architecture</h4>
          <span>Central Auditorium &bull; Gujarat</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Bespoke Event Planning Services -->
<section class="features-section">
  <div class="container">
    <div class="section-title" style="text-align:center; max-width:700px; margin:0 auto 40px auto;">
      <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
        Royal Services
      </span>
      <h2>Bespoke Indian Event Management</h2>
      <div class="gold-divider"></div>
      <p>From concept architecture to shahi hospitality, our experienced event directors deliver turnkey luxury celebrations tailored to royal perfection.</p>
    </div>

    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-archway"></i>
        </div>
        <h4>Royal Mandap &amp; Floral Architecture</h4>
        <p>Bespoke carved wooden structures, fragrant Mogra and imported Dutch blooms, thematic Rajputana arches, and ethereal Vedic mandap designs.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-music"></i>
        </div>
        <h4>Sufi, Folk &amp; Celebrity Artist Management</h4>
        <p>Exclusive direct artist booking for celebrated Sufi Qawwals, classical sitar maestros, legendary Garba vocalists, and Bollywood performers.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-champagne-glasses"></i>
        </div>
        <h4>Royal Sangeet &amp; Gala Production</h4>
        <p>Concert-grade line-array sound systems, intelligent kinetic beam lighting, stadium LED backdrops, pyrotechnics, and professional choreography.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <i class="fa-solid fa-utensils"></i>
        </div>
        <h4>Shahi Rajwadi &amp; Gourmet Catering</h4>
        <p>Regal Kathiyawadi feasts, authentic Gujarati thals, Awadhi royal delicacies, live interactive culinary counters, and impeccable shahi silver service.</p>
      </div>
    </div>
  </div>
</section>

<!-- Royal Patrons & Testimonials Carousel -->
<section class="testimonials-section">
  <div class="container">
    <div class="section-title" style="text-align:center; max-width:700px; margin:0 auto;">
      <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
        Patron Reviews
      </span>
      <h2>Words from Our Royal Families</h2>
      <div class="gold-divider"></div>
      <p>Reflections from families and corporate organizations who entrusted Bhakti Events with their most precious moments.</p>
    </div>

    <div class="testimonials-grid">
      <div class="testimonial-card">
        <i class="fa-solid fa-quote-left quote-icon"></i>
        <p class="testimonial-text">
          "Bhakti Events orchestrated our daughter's 3-day destination wedding at Udaipur with absolute perfection. From the Rajputana floral mandap to the shahi royal banquet, our 600 guests were mesmerized. Truly Gujarat's finest event creators."
        </p>
        <div class="client-profile">
          <div class="client-avatar">R</div>
          <div class="client-info">
            <h5>Rajendrasinh Jadeja</h5>
            <span>Destination Wedding &bull; Rajkot &amp; Udaipur</span>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <i class="fa-solid fa-quote-left quote-icon"></i>
        <p class="testimonial-text">
          "The Navratri Rasleela Mahotsav production in Gondal was executed with extraordinary precision. Flawless acoustic sound, strict VIP access control, and stunning traditional stage lighting. We have partnered with them since 2018."
        </p>
        <div class="client-profile">
          <div class="client-avatar">H</div>
          <div class="client-info">
            <h5>Harshadbhai Patel</h5>
            <span>Cultural Festival Committee &bull; Gondal</span>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <i class="fa-solid fa-quote-left quote-icon"></i>
        <p class="testimonial-text">
          "Our corporate leadership summit required top-tier hospitality, laser visuals, and seamless registration. Bhakti Events managed all 250 executive delegates flawlessly. Their transparent budget calculator and WhatsApp concierge were invaluable."
        </p>
        <div class="client-profile">
          <div class="client-avatar">M</div>
          <div class="client-info">
            <h5>Meera Shah</h5>
            <span>Corporate Communications &bull; Ahmedabad</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- The Royal Assurance (Why Gujarat Chooses Bhakti Events) -->
<section style="padding:75px 0; background:#FFFFFF; border-top:1px solid var(--gold-border); border-bottom:1px solid var(--gold-border);">
  <div class="container">
    <div class="section-title" style="text-align:center; max-width:720px; margin:0 auto 45px auto;">
      <span style="color:var(--burgundy); text-transform:uppercase; font-size:12px; font-weight:700; letter-spacing:1.5px; display:block; margin-bottom:6px;">
        Heritage &amp; Integrity
      </span>
      <h2 style="font-size:32px; color:var(--burgundy);">The Royal Assurance</h2>
      <div class="gold-divider"></div>
      <p>For more than a decade, distinguished families across Gujarat have entrusted us with their most momentous celebrations. Here is what defines our commitment.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:28px;">
      <div style="background:var(--ivory-soft); border:1.5px solid var(--gold-border); border-radius:16px; padding:28px 24px; text-align:center; box-shadow:0 8px 24px rgba(122,28,46,0.05); transition:transform 0.3s ease;">
        <div style="width:56px; height:56px; border-radius:50%; background:var(--burgundy-gradient); color:var(--gold-light); display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:16px; border:1px solid var(--gold);">
          <i class="fa-solid fa-crown"></i>
        </div>
        <h4 style="color:var(--burgundy); font-size:19px; margin-bottom:10px;">10+ Years Heritage</h4>
        <p style="font-size:13.5px; color:var(--charcoal-muted); line-height:1.6; margin:0;">Founded in 2014 in Gondal, we bring unmatched heritage decor expertise and trusted vendor relationships across Saurashtra.</p>
      </div>

      <div style="background:var(--ivory-soft); border:1.5px solid var(--gold-border); border-radius:16px; padding:28px 24px; text-align:center; box-shadow:0 8px 24px rgba(122,28,46,0.05); transition:transform 0.3s ease;">
        <div style="width:56px; height:56px; border-radius:50%; background:var(--burgundy-gradient); color:var(--gold-light); display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:16px; border:1px solid var(--gold);">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h4 style="color:var(--burgundy); font-size:19px; margin-bottom:10px;">100% Verified E-Passes</h4>
        <p style="font-size:13.5px; color:var(--charcoal-muted); line-height:1.6; margin:0;">Every event pass is digitally encrypted with unique security tokens and QR scanning, preventing duplicate entry and overcrowding.</p>
      </div>

      <div style="background:var(--ivory-soft); border:1.5px solid var(--gold-border); border-radius:16px; padding:28px 24px; text-align:center; box-shadow:0 8px 24px rgba(122,28,46,0.05); transition:transform 0.3s ease;">
        <div style="width:56px; height:56px; border-radius:50%; background:var(--burgundy-gradient); color:var(--gold-light); display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:16px; border:1px solid var(--gold);">
          <i class="fa-solid fa-handshake"></i>
        </div>
        <h4 style="color:var(--burgundy); font-size:19px; margin-bottom:10px;">Zero-Markup Direct Access</h4>
        <p style="font-size:13.5px; color:var(--charcoal-muted); line-height:1.6; margin:0;">Direct artist bookings, shahi catering procurement, and royal palace venue reservations with 100% transparent pricing.</p>
      </div>

      <div style="background:var(--ivory-soft); border:1.5px solid var(--gold-border); border-radius:16px; padding:28px 24px; text-align:center; box-shadow:0 8px 24px rgba(122,28,46,0.05); transition:transform 0.3s ease;">
        <div style="width:56px; height:56px; border-radius:50%; background:var(--burgundy-gradient); color:var(--gold-light); display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:16px; border:1px solid var(--gold);">
          <i class="fa-solid fa-user-tie"></i>
        </div>
        <h4 style="color:var(--burgundy); font-size:19px; margin-bottom:10px;">On-Ground Directorship</h4>
        <p style="font-size:13.5px; color:var(--charcoal-muted); line-height:1.6; margin:0;">Our senior celebration directors supervise every celebration in person, ensuring seamless timeline execution and royal hospitality.</p>
      </div>
    </div>
  </div>
</section>

<!-- Grand Consultation Callout -->
<section style="padding:65px 0; background:linear-gradient(135deg, var(--burgundy-dark) 0%, var(--burgundy) 100%); color:#fff; position:relative; overflow:hidden;">
  <div style="position:absolute; top:-30px; right:-30px; font-size:240px; color:rgba(197,160,89,0.05); pointer-events:none;">
    <i class="fa-solid fa-crown"></i>
  </div>
  <div class="container" style="position:relative; z-index:2; text-align:center; max-width:820px;">
    <span class="badge-pill" style="margin-bottom:14px; background:rgba(197,160,89,0.18); border-color:var(--gold); color:var(--gold-light);">
      <i class="fa-solid fa-gem"></i> Bespoke Event Concierge
    </span>
    <h2 style="font-size:34px; color:var(--gold-light); font-family:var(--font-heading); margin-bottom:16px;">
      Planning a Grand Wedding or Gala in Gujarat?
    </h2>
    <p style="font-size:16px; color:#F5EBE6; line-height:1.7; margin-bottom:28px;">
      Visit our flagship design studio at <strong>Tirumala Shopping Mall, In Gundala Darwaja, Gondal</strong>, or connect directly with our chief celebration director for personalized venue booking and decor conceptualization.
    </p>
    <div style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
      <a href="https://wa.me/919825661046?text=<?= urlencode('Hello Bhakti Events! I would like to schedule a personal consultation for an upcoming celebration in Gujarat.') ?>" 
         target="_blank" 
         rel="noopener" 
         class="btn btn-secondary" 
         style="padding:14px 30px; font-size:15px;">
        <i class="fa-brands fa-whatsapp me-2" style="color:#25D366; font-size:18px;"></i> Inquire on WhatsApp
      </a>
      <a href="tel:+919825661046" class="btn btn-outline-white" style="padding:14px 28px; font-size:15px; border-color:var(--gold); color:#fff;">
        <i class="fa-solid fa-phone me-2" style="color:var(--gold-light);"></i> +91 9825661046
      </a>
      <a href="contact.php" class="btn btn-outline-white" style="padding:14px 28px; font-size:15px;">
        <i class="fa-solid fa-envelope me-2"></i> Book Studio Visit
      </a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
