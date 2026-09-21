<?php
// includes/footer.php - Royal Indian Luxury Footer & Academic Modal
$isSubfolder = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'user' || basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin');
$base = $isSubfolder ? '../' : './';
?>
</main>

<!-- Floating WhatsApp Concierge Button -->
<a href="https://wa.me/919825661046?text=Namaste%20Bhakti%20Events!%20I%20am%20visiting%20your%20website%20and%20would%20like%20to%20inquire%20about%20event%20booking." target="_blank" rel="noopener noreferrer" class="floating-whatsapp" aria-label="Inquire on WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- Global Footer -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      
      <!-- Col 1: Brand & Atelier Story -->
      <div class="footer-col">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
          <div style="width:36px; height:36px; border-radius:8px; background:var(--burgundy-gradient); border:1.5px solid var(--secondary); display:flex; align-items:center; justify-content:center; color:var(--secondary); font-size:18px;">
            <i class="fa-solid fa-crown"></i>
          </div>
          <div>
            <h4 style="margin:0; font-size:20px; color:#FFFFFF;">BHAKTI <span style="color:var(--secondary);">EVENTS</span></h4>
            <span style="font-size:10px; color:var(--secondary-light); text-transform:uppercase; letter-spacing:1.5px;">Est. 2014 &bull; Gondal, Gujarat</span>
          </div>
        </div>
        <p>Premier celebration management and royal Indian wedding atelier. We curate magnificent palace mandaps, soulful Sufi mehfils, heritage Navratri rasleela, and bespoke gala experiences with timeless grace.</p>
        <div style="display:flex; gap:12px; margin-top:16px;">
          <a href="https://wa.me/919825661046" target="_blank" style="color:var(--secondary); font-size:18px;"><i class="fa-brands fa-whatsapp"></i></a>
          <a href="https://instagram.com/bhakti_creation22" target="_blank" style="color:var(--secondary); font-size:18px;"><i class="fa-brands fa-instagram"></i></a>
          <a href="mailto:hp6224974@gmail.com" style="color:var(--secondary); font-size:18px;"><i class="fa-solid fa-envelope"></i></a>
          <a href="tel:+919825661046" style="color:var(--secondary); font-size:18px;"><i class="fa-solid fa-phone"></i></a>
        </div>
      </div>

      <!-- Col 2: Quick Links -->
      <div class="footer-col">
        <h4>Navigation</h4>
        <ul class="footer-links">
          <li><a href="<?= $base ?>index.php"><i class="fa-solid fa-angle-right"></i> Home Showcase</a></li>
          <li><a href="<?= $base ?>events.php"><i class="fa-solid fa-angle-right"></i> Royal Celebrations</a></li>
          <li><a href="<?= $base ?>contact.php"><i class="fa-solid fa-angle-right"></i> Contact &amp; Venue</a></li>
          <li><a href="<?= $base ?>admin/index.php"><i class="fa-solid fa-angle-right"></i> Staff / Admin Portal</a></li>
        </ul>
      </div>

      <!-- Col 3: Signature Celebrations -->
      <div class="footer-col">
        <h4>Signature Events</h4>
        <ul class="footer-links">
          <li><a href="<?= $base ?>events.php?category=1"><i class="fa-solid fa-angle-right"></i> Royal Weddings</a></li>
          <li><a href="<?= $base ?>events.php?category=2"><i class="fa-solid fa-angle-right"></i> Sufi &amp; Classical Nights</a></li>
          <li><a href="<?= $base ?>events.php?category=3"><i class="fa-solid fa-angle-right"></i> Navratri Garba Raas</a></li>
          <li><a href="<?= $base ?>events.php?category=4"><i class="fa-solid fa-angle-right"></i> Sangeet &amp; Symphony</a></li>
          <li><a href="<?= $base ?>events.php?category=5"><i class="fa-solid fa-angle-right"></i> Corporate Galas</a></li>
        </ul>
      </div>

      <!-- Col 4: Boutique Store & Office -->
      <div class="footer-col">
        <h4>Boutique Office</h4>
        <p style="margin-bottom:8px;">
          <i class="fa-solid fa-location-dot text-gold" style="margin-right:6px;"></i>
          Tirumala Shopping Mall, In Gundala Darwaja, Gondal, Gujarat - 360311
        </p>
        <p style="margin-bottom:8px;">
          <i class="fa-solid fa-phone text-gold" style="margin-right:6px;"></i>
          <a href="tel:+919825661046" style="color:#FFF;">+91 9825661046</a>
        </p>
        <p style="margin-bottom:8px;">
          <i class="fa-solid fa-envelope text-gold" style="margin-right:6px;"></i>
          <a href="mailto:hp6224974@gmail.com" style="color:#FFF;">hp6224974@gmail.com</a>
        </p>
        <p>
          <i class="fa-solid fa-clock text-gold" style="margin-right:6px;"></i>
          Mon - Sat: 10:00 AM - 8:30 PM
        </p>
      </div>

    </div>

    <!-- Bottom Copyright Strip -->
    <div class="footer-bottom">
      <div>
        &copy; 2014 - 2026 <strong>Bhakti Events &amp; Celebrations</strong>. All Rights Reserved.
      </div>
      <div style="display:flex; align-items:center; gap:20px;">
        <span>Handcrafted with Royal Elegance in Gondal, Gujarat</span>
        <a href="javascript:void(0)" onclick="openProjectModal()" style="color:#64748B; font-size:11.5px;">
          <i class="fa-solid fa-graduation-cap"></i> Academic Dossier
        </a>
      </div>
    </div>
  </div>
</footer>

<!-- BCA Project Academic Credential Modal (Discreetly accessible for viva evaluation) -->
<div id="projectModal" class="modal" style="display:none;" role="dialog" aria-labelledby="modalTitle">
  <div class="modal-box">
    <span class="close-btn" onclick="closeProjectModal()">&times;</span>
    <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
      <div style="width:44px; height:44px; border-radius:10px; background:var(--primary-subtle); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:20px; border:1px solid var(--border-gold);">
        <i class="fa-solid fa-graduation-cap"></i>
      </div>
      <div>
        <h3 id="modalTitle" style="color:var(--primary); margin:0; font-size:20px; font-family:var(--font-heading);">BCA Semester 5 Project Credentials</h3>
        <p style="margin:0; font-size:12.5px; color:var(--text-secondary);">University Evaluation Dossier &bull; Academic Year 2026</p>
      </div>
    </div>

    <table style="width:100%; border-collapse: collapse; text-align:left; font-size:14px; margin-top:14px;">
      <tbody>
        <tr style="border-bottom:1px solid #F4EFEB;">
          <td style="padding:10px 6px; font-weight:700; color:var(--primary); width:40%;">Project Title:</td>
          <td style="padding:10px 6px; color:var(--text-primary); font-weight:600;">Bhakti Events — Royal Event Management &amp; Ticketing System</td>
        </tr>
        <tr style="border-bottom:1px solid #F4EFEB;">
          <td style="padding:10px 6px; font-weight:700; color:var(--primary);">Student Name:</td>
          <td style="padding:10px 6px; color:var(--text-primary); font-weight:600;">Parmar Ankita Pankajbhai</td>
        </tr>
        <tr style="border-bottom:1px solid #F4EFEB;">
          <td style="padding:10px 6px; font-weight:700; color:var(--primary);">Enrollment No:</td>
          <td style="padding:10px 6px; color:var(--secondary-dark); font-weight:700; font-family:monospace;">24CS002UG01020</td>
        </tr>
        <tr style="border-bottom:1px solid #F4EFEB;">
          <td style="padding:10px 6px; font-weight:700; color:var(--primary);">Project Guide:</td>
          <td style="padding:10px 6px; color:var(--text-primary);">Prof. Aryan More</td>
        </tr>
        <tr style="border-bottom:1px solid #F4EFEB;">
          <td style="padding:10px 6px; font-weight:700; color:var(--primary);">Course &amp; Semester:</td>
          <td style="padding:10px 6px; color:var(--text-primary);">Bachelor of Computer Applications (BCA) — Semester 5</td>
        </tr>
        <tr style="border-bottom:1px solid #F4EFEB;">
          <td style="padding:10px 6px; font-weight:700; color:var(--primary);">Project Domain:</td>
          <td style="padding:10px 6px; color:var(--text-primary);">Web Development / Relational Database Systems (RDBMS)</td>
        </tr>
        <tr>
          <td style="padding:10px 6px; font-weight:700; color:var(--primary);">Key Architecture:</td>
          <td style="padding:10px 6px; color:var(--text-secondary); font-size:12.5px;">Normalized 3NF MySQL Architecture, ACID Database Transactions with <code>SELECT ... FOR UPDATE</code> Row Locking, Bcrypt Authentication, and Printable Royal Passes.</td>
        </tr>
      </tbody>
    </table>

    <div style="margin-top:24px;">
      <button type="button" class="btn btn-primary" style="width:100%;" onclick="closeProjectModal()">Close Window</button>
    </div>
  </div>
</div>

<!-- Public Frontend JavaScript with Cache Buster -->
<script src="<?= $base ?>assets/js/main.js?v=<?= time() ?>"></script>

</body>
</html>
