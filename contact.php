<?php
// contact.php - Royal Office Contact & Celebration Inquiries
require_once 'config/db.php';
require_once 'includes/functions.php';

$pageTitle = "Contact Royal Event Studio & Concierge";
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? $_POST['fullname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $service    = trim($_POST['service'] ?? '');
    $eventDate  = trim($_POST['event_date'] ?? '');
    $venue      = trim($_POST['venue'] ?? '');
    $guests     = trim($_POST['guests'] ?? '');
    $message    = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        $submitted = true;
        $inqRef = 'INQ-' . date('Y') . '-' . rand(1000, 9999);
        try {
            $inqStmt = $pdo->prepare("INSERT INTO inquiries 
                (inquiry_ref, fullname, email, phone, service, event_date, venue, guests, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')");
            $inqStmt->execute([$inqRef, $name, $email, $phone, $service, $eventDate, $venue, $guests, $message]);
            set_flash('success', 'Namaste ' . htmlspecialchars($name) . '! Your royal celebration inquiry (' . $inqRef . ') has been registered with our Gondal directorship. Our celebration directors will connect with you within 4 business hours.');
        } catch (Exception $e) {
            set_flash('success', 'Namaste ' . htmlspecialchars($name) . '! Your royal celebration inquiry has been received. Our celebration directors will connect with you within 4 business hours.');
        }
    } else {
        set_flash('danger', 'Please provide your full name, email address, and celebration details.');
    }
}

include 'includes/header.php';
?>

<!-- Luxury Page Header Banner -->
<section style="background:linear-gradient(135deg, #250409 0%, #4E0A16 50%, #250409 100%); color:#fff; padding:75px 20px 85px; border-bottom:2px solid var(--gold); position:relative; overflow:hidden;">
  <!-- Watermark Crest -->
  <div style="position:absolute; right:-40px; top:-40px; font-size:260px; color:rgba(197,160,89,0.04); pointer-events:none;">
    <i class="fa-solid fa-crown"></i>
  </div>

  <div class="container" style="position:relative; z-index:2;">
    <!-- Breadcrumb -->
    <div style="display:flex; align-items:center; gap:8px; font-size:12px; color:var(--gold-light); margin-bottom:16px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">
      <a href="index.php" style="color:var(--gold-light); text-decoration:none;">Home</a>
      <span style="opacity:0.6;">&rsaquo;</span>
      <span style="color:#FFF;">Royal Studio &amp; Concierge</span>
    </div>

    <div style="max-width:820px;">
      <span class="badge-pill" style="background:rgba(197,160,89,0.22); color:var(--gold-light); border-color:var(--gold); margin-bottom:14px; font-size:12px;">
        <i class="fa-solid fa-crown"></i> Bespoke Event Atelier &bull; Est. 2014 &bull; Gondal, Gujarat
      </span>
      <h1 style="color:#FFFFFF; font-size:clamp(32px, 4.5vw, 46px); font-family:var(--font-heading); font-weight:800; line-height:1.2; margin-bottom:14px;">
        Connect with Our Royal Event Directors
      </h1>
      <p style="color:#F5EBE6; font-size:clamp(15px, 1.8vw, 17px); font-weight:300; line-height:1.7; margin:0;">
        Whether conceptualizing a multi-day Rajputana palace wedding in Udaipur, a soulful candlelit Sufi mehfil on the Sabarmati riverfront, or an authentic royal Garba mahotsav in Gondal, our master coordinators ensure turnkey grandeur and flawless hospitality.
      </p>
    </div>
  </div>
</section>

<!-- Main Interactive Contact & Atelier Grid -->
<section style="padding:60px 0 90px; background:var(--bg-base);">
  <div class="container">
    <div style="display:grid; grid-template-columns:1.22fr 1fr; gap:40px; align-items:start;">
      
      <!-- Left Column: The Consultation Form Card -->
      <div style="background:#FFFFFF; border-radius:20px; border:1.5px solid var(--gold-border); padding:42px 38px; box-shadow:0 20px 50px rgba(122, 28, 46, 0.08);">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
          <div style="width:40px; height:40px; border-radius:10px; background:var(--burgundy-gradient); color:var(--gold-light); display:flex; align-items:center; justify-content:center; font-size:18px; border:1px solid var(--gold);">
            <i class="fa-solid fa-feather-pointed"></i>
          </div>
          <h2 style="font-size:26px; margin:0; color:var(--burgundy); font-family:var(--font-heading);">
            Request a Royal Consultation
          </h2>
        </div>
        <div class="gold-divider" style="margin:12px 0 18px 0; justify-content:flex-start;"></div>
        <p style="color:var(--charcoal-muted); font-size:14px; margin-bottom:28px; line-height:1.6;">
          Share your event parameters, tentative venue, and attendance below. Our senior celebration director will review your requirements and connect with you within 4 business hours.
        </p>

        <form action="contact.php" method="POST" id="consultationForm">
          <!-- Full Name -->
          <div class="form-group">
            <label for="name"><i class="fa-solid fa-user"></i> Your Full Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Maharawal Rajendrasinh Jadeja" required value="<?= is_logged_in() ? e($_SESSION['fullname']) : '' ?>">
          </div>

          <!-- Email & Phone in 2 Columns -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
            <div class="form-group">
              <label for="email"><i class="fa-solid fa-envelope"></i> Email Address *</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="name@domain.com" required value="<?= is_logged_in() ? e($_SESSION['email']) : '' ?>">
            </div>
            <div class="form-group">
              <label for="phone"><i class="fa-solid fa-phone"></i> Phone / WhatsApp Number *</label>
              <input type="tel" id="phone" name="phone" class="form-control" placeholder="+91 98256 61046" required value="<?= is_logged_in() ? e($_SESSION['phone'] ?? '') : '' ?>">
            </div>
          </div>

          <!-- Celebration Type & Date -->
          <div style="display:grid; grid-template-columns:1.2fr 1fr; gap:18px;">
            <div class="form-group">
              <label for="service"><i class="fa-solid fa-crown"></i> Celebration Type</label>
              <select id="service" name="service" class="form-control">
                <option value="Royal Palace Destination Wedding (3 Days)">Royal Palace Destination Wedding (3 Days)</option>
                <option value="Royal Sangeet & Choreography Gala">Royal Sangeet &amp; Choreography Gala</option>
                <option value="Soulful Sufi & Classical Ghazal Mehfil">Soulful Sufi &amp; Classical Ghazal Mehfil</option>
                <option value="Grand Navratri Cultural Garba Mahotsav">Grand Navratri Cultural Garba Mahotsav</option>
                <option value="Corporate Leadership Banquet & Awards">Corporate Leadership Banquet &amp; Awards</option>
                <option value="General Pass / VIP Table Reservation">General Pass / VIP Table Reservation</option>
              </select>
            </div>
            <div class="form-group">
              <label for="eventDate"><i class="fa-regular fa-calendar-check"></i> Tentative Date / Season</label>
              <input type="text" id="eventDate" name="event_date" class="form-control" placeholder="e.g. Winter 2026 / Nov 24">
            </div>
          </div>

          <!-- Venue City & Guest Count -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
            <div class="form-group">
              <label for="venue"><i class="fa-solid fa-archway"></i> Venue Tier &amp; Location</label>
              <select id="venue" name="venue" class="form-control">
                <option value="Heritage Lake Palace (Udaipur / Jaipur)">Heritage Lake Palace (Udaipur / Jaipur)</option>
                <option value="Royal Riverside Heritage Grounds (Gondal)">Royal Riverside Grounds (Gondal)</option>
                <option value="Riverfront Amphitheater (Ahmedabad)">Riverfront Amphitheater (Ahmedabad)</option>
                <option value="5-Star Luxury Hotel Ballroom (Gandhinagar / Mumbai)">5-Star Luxury Hotel Ballroom</option>
                <option value="Other Private Estate or Heritage Lawns">Other Private Estate or Heritage Lawns</option>
              </select>
            </div>
            <div class="form-group">
              <label for="guests"><i class="fa-solid fa-users"></i> Estimated Attendance</label>
              <select id="guests" name="guests" class="form-control">
                <option value="100 - 250 Guests">Intimate Gathering (100 - 250 Guests)</option>
                <option value="250 - 500 Guests" selected>Royal Gathering (250 - 500 Guests)</option>
                <option value="500 - 1000 Guests">Grand Shahi Celebration (500 - 1,000 Guests)</option>
                <option value="1000+ Guests">Mega Celebration (1,000+ Guests)</option>
              </select>
            </div>
          </div>

          <!-- Requirements Textarea -->
          <div class="form-group">
            <label for="message"><i class="fa-solid fa-pen-nib"></i> Celebration Vision &amp; Special Requirements *</label>
            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Share your theme preferences, floral mandap vision, catering style (e.g. Shahi Rajwadi Kathiyawadi, Awadhi banquet), artist bookings, or specific palace estate preferences..." required></textarea>
          </div>

          <!-- Primary Submit Button -->
          <button type="submit" class="btn btn-primary" style="width:100%; padding:15px; font-size:16px; font-weight:700; border-radius:12px; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 8px 25px rgba(122,28,46,0.35);">
            <i class="fa-solid fa-paper-plane"></i> Send Consultation Request
          </button>
        </form>

        <!-- Fast WhatsApp Alternative -->
        <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-top:16px; font-size:13px; color:var(--charcoal-muted);">
          <span>Need an immediate response?</span>
          <a href="https://wa.me/919825661046?text=<?= urlencode('Namaste Bhakti Events! I would like to schedule a personal consultation for an upcoming celebration.') ?>" 
             target="_blank" 
             rel="noopener" 
             style="color:#10B981; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp Directly
          </a>
        </div>
      </div>

      <!-- Right Column: Atelier Directory, Map & FAQ -->
      <div>
        
        <!-- Quick Contact Channels Row -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
          <!-- Email Card -->
          <div style="background:#FFFFFF; border:1.5px solid var(--gold-border); border-radius:14px; padding:22px 20px; box-shadow:var(--shadow-sm); transition:transform 0.2s ease;">
            <div style="width:44px; height:44px; border-radius:10px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:19px; margin-bottom:12px;">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <span style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--secondary-dark); font-weight:700; display:block; margin-bottom:2px;">
              Email Concierge
            </span>
            <strong style="font-size:15px; color:var(--burgundy); display:block; margin-bottom:4px;">
              <a href="mailto:hp6224974@gmail.com" style="color:inherit; text-decoration:none;">hp6224974@gmail.com</a>
            </strong>
            <span style="font-size:12px; color:var(--charcoal-muted);">Responses within 4 business hours</span>
          </div>

          <!-- Phone Card -->
          <div style="background:#FFFFFF; border:1.5px solid var(--gold-border); border-radius:14px; padding:22px 20px; box-shadow:var(--shadow-sm); transition:transform 0.2s ease;">
            <div style="width:44px; height:44px; border-radius:10px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; font-size:19px; margin-bottom:12px;">
              <i class="fa-solid fa-phone"></i>
            </div>
            <span style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--secondary-dark); font-weight:700; display:block; margin-bottom:2px;">
              Direct Helpline
            </span>
            <strong style="font-size:15px; color:var(--burgundy); display:block; margin-bottom:4px;">
              <a href="tel:+919825661046" style="color:inherit; text-decoration:none;">+91 9825661046</a>
            </strong>
            <span style="font-size:12px; color:var(--charcoal-muted);">Mon–Sat: 10:00 AM – 8:30 PM</span>
          </div>
        </div>

        <!-- Flagship Design Studio & Headquarters -->
        <div style="background:#FFFFFF; border:1.5px solid var(--gold-border); border-radius:16px; padding:26px 24px; box-shadow:var(--shadow-sm); margin-bottom:24px;">
          <div style="display:flex; align-items:flex-start; gap:14px;">
            <div style="width:48px; height:48px; border-radius:12px; background:var(--burgundy-gradient); color:var(--gold-light); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; border:1px solid var(--gold);">
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
              <span style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--secondary-dark); font-weight:700; display:block;">
                Flagship Design Studio &amp; Headquarters
              </span>
              <h4 style="font-size:18px; color:var(--burgundy); margin:2px 0 6px; font-family:var(--font-heading);">
                Bhakti Events &amp; Celebrations
              </h4>
              <p style="font-size:13.5px; color:var(--charcoal); margin:0 0 8px; line-height:1.5;">
                Tirumala Shopping Mall, In Gundala Darwaja,<br>
                Gondal, Gujarat - 360311, India
              </p>
              <div style="font-size:12px; color:var(--charcoal-muted); display:flex; flex-direction:column; gap:3px;">
                <span><i class="fa-regular fa-clock me-1" style="color:var(--gold);"></i> <strong>Studio Hours:</strong> Mon - Sat: 10:00 AM - 8:30 PM</span>
                <span><i class="fa-solid fa-gem me-1" style="color:var(--gold);"></i> <strong>Sunday:</strong> Reserved for Exclusive Royal Inquiries by Appointment</span>
              </div>
            </div>
          </div>

          <div style="margin-top:18px;">
            <a href="https://wa.me/919825661046?text=<?= urlencode('Hello Bhakti Events! I would like to schedule a personal visit to your Gondal design studio.') ?>" 
               target="_blank" 
               rel="noopener" 
               class="btn btn-whatsapp" 
               style="width:100%; padding:13px; font-size:14px; font-weight:700; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:8px;">
              <i class="fa-brands fa-whatsapp" style="font-size:18px;"></i> Direct WhatsApp Concierge (+91 9825661046)
            </a>
          </div>
        </div>

        <!-- Interactive Google Map Frame of Gondal Studio -->
        <div style="border-radius:16px; overflow:hidden; border:1.5px solid var(--gold-border); margin-bottom:24px; box-shadow:var(--shadow-sm); height:220px; position:relative;">
          <iframe 
            src="https://maps.google.com/maps?q=Tirumala+Shopping+Mall+Gundala+Darwaja+Gondal+Gujarat&t=&z=15&ie=UTF8&iwloc=&output=embed" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Bhakti Events Gondal Flagship Studio Map">
          </iframe>
        </div>

        <!-- Frequently Asked Royal Inquiries (FAQ Cards) -->
        <div style="background:#FFFFFF; border-radius:16px; border:1.5px solid var(--gold-border); padding:28px 24px; box-shadow:var(--shadow-sm);">
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
            <i class="fa-solid fa-circle-question" style="color:var(--gold); font-size:18px;"></i>
            <h4 style="font-size:20px; margin:0; font-family:var(--font-heading); color:var(--burgundy);">
              Frequently Asked Inquiries
            </h4>
          </div>
          <div class="gold-divider" style="margin:10px 0 18px 0; justify-content:flex-start;"></div>
          
          <div style="margin-bottom:14px; padding-bottom:12px; border-bottom:1px dashed var(--gold-border);">
            <strong style="font-size:14px; color:var(--burgundy-dark); display:block; margin-bottom:4px;">
              Q: Does Bhakti Events orchestrate weddings outside Gondal?
            </strong>
            <p style="font-size:13px; color:var(--charcoal-muted); margin:0; line-height:1.55;">
              Yes! For over a decade, we have planned and executed magnificent destination weddings across Udaipur, Jaipur, Jodhpur, Rajkot, Ahmedabad, Vadodara, and coastal private beach resorts.
            </p>
          </div>

          <div style="margin-bottom:14px; padding-bottom:12px; border-bottom:1px dashed var(--gold-border);">
            <strong style="font-size:14px; color:var(--burgundy-dark); display:block; margin-bottom:4px;">
              Q: How does digital pass verification operate at celebration gates?
            </strong>
            <p style="font-size:13px; color:var(--charcoal-muted); margin:0; line-height:1.55;">
              Every pass reserved through our portal generates a cryptographically signed digital E-Pass voucher with unique QR barcodes. Our on-ground security team validates each pass via digital scanners in real time.
            </p>
          </div>

          <div style="margin-bottom:14px; padding-bottom:12px; border-bottom:1px dashed var(--gold-border);">
            <strong style="font-size:14px; color:var(--burgundy-dark); display:block; margin-bottom:4px;">
              Q: Can we schedule private menu tastings and decor mockups?
            </strong>
            <p style="font-size:13px; color:var(--charcoal-muted); margin:0; line-height:1.55;">
              Yes. At our Gondal design studio, we provide physical fabric draping swatches, 3D floral mandap miniature models, and coordinated Shahi Rajwadi catering tastings by appointment.
            </p>
          </div>

          <div>
            <strong style="font-size:14px; color:var(--burgundy-dark); display:block; margin-bottom:4px;">
              Q: What is the typical lead time required for royal celebrations?
            </strong>
            <p style="font-size:13px; color:var(--charcoal-muted); margin:0; line-height:1.55;">
              While premier heritage palace estates require 3 to 6 months advance reservation, our rapid on-ground production network can execute signature events in under 30 days.
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Bottom Royal Assurance Strip -->
<section style="padding:45px 0; background:#FFFFFF; border-top:1px solid var(--gold-border); border-bottom:1px solid var(--gold-border);">
  <div class="container">
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:24px; text-align:center;">
      <div style="padding:10px;">
        <i class="fa-solid fa-gem" style="font-size:24px; color:var(--gold); margin-bottom:10px; display:inline-block;"></i>
        <h5 style="font-size:15px; color:var(--burgundy); margin-bottom:4px;">Complimentary Vision Session</h5>
        <p style="font-size:12.5px; color:var(--charcoal-muted); margin:0;">Zero-obligation decor conceptualization</p>
      </div>
      <div style="padding:10px;">
        <i class="fa-solid fa-clock-rotate-left" style="font-size:24px; color:var(--gold); margin-bottom:10px; display:inline-block;"></i>
        <h5 style="font-size:15px; color:var(--burgundy); margin-bottom:4px;">4-Hour Response Guarantee</h5>
        <p style="font-size:12.5px; color:var(--charcoal-muted); margin:0;">Prompt consultation by celebration directors</p>
      </div>
      <div style="padding:10px;">
        <i class="fa-solid fa-scale-balanced" style="font-size:24px; color:var(--gold); margin-bottom:10px; display:inline-block;"></i>
        <h5 style="font-size:15px; color:var(--burgundy); margin-bottom:4px;">100% Transparent Projections</h5>
        <p style="font-size:12.5px; color:var(--charcoal-muted); margin:0;">Zero hidden venue or agency markups</p>
      </div>
      <div style="padding:10px;">
        <i class="fa-solid fa-shield-halved" style="font-size:24px; color:var(--gold); margin-bottom:10px; display:inline-block;"></i>
        <h5 style="font-size:15px; color:var(--burgundy); margin-bottom:4px;">Turnkey Directorship</h5>
        <p style="font-size:12.5px; color:var(--charcoal-muted); margin:0;">Full protocol, bridal escort &amp; valet management</p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
