/**
 * Bhakti Events & Celebrations — Public Frontend Interactivity & Luxury Calculators
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Drawer Navigation Toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const navLinks = document.getElementById('navLinks');
  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', () => {
      navLinks.classList.toggle('mobile-open');
    });
  }

  // 2. User Account Dropdown
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.classList.remove('show');
      }
    });
  }

  // 3. Scroll Progress Bar & Sticky Header Transformation
  const progressBar = document.getElementById('scrollProgressBar');
  const header = document.querySelector('.main-header');
  const scrollIndicator = document.getElementById('scrollDownIndicator');

  window.addEventListener('scroll', () => {
    const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = height > 0 ? (winScroll / height) * 100 : 0;

    if (progressBar) {
      progressBar.style.width = scrolled + '%';
    }

    if (header) {
      if (winScroll > 60) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }

    if (scrollIndicator) {
      if (winScroll > 80) {
        scrollIndicator.style.opacity = '0';
        scrollIndicator.style.pointerEvents = 'none';
      } else {
        scrollIndicator.style.opacity = '1';
        scrollIndicator.style.pointerEvents = 'auto';
      }
    }
  });

  // 4. Animated Counter Numbers (IntersectionObserver)
  const statNumbers = document.querySelectorAll('.stat-num');
  if ('IntersectionObserver' in window && statNumbers.length > 0) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const text = el.textContent.trim();
          const match = text.match(/(\d+)(\+?%?)/);
          if (match) {
            const targetVal = parseInt(match[1], 10);
            const suffix = match[2] || '';
            let current = 0;
            const step = Math.max(1, Math.ceil(targetVal / 30));
            const timer = setInterval(() => {
              current += step;
              if (current >= targetVal) {
                current = targetVal;
                clearInterval(timer);
              }
              el.textContent = current + suffix;
            }, 35);
          }
          obs.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    statNumbers.forEach((num) => observer.observe(num));
  }

  // 5. Interactive Royal Celebration Budget Estimator
  const guestSlider = document.getElementById('estGuests');
  const guestDisplay = document.getElementById('estGuestsVal');
  const typeSelect = document.getElementById('estType');
  const venueSelect = document.getElementById('estVenue');
  const decorSelect = document.getElementById('estDecor');
  const cateringSelect = document.getElementById('estCatering');
  const artistSelect = document.getElementById('estArtist');

  const bVenue = document.getElementById('receiptVenue');
  const bDecor = document.getElementById('receiptDecor');
  const bCatering = document.getElementById('receiptCatering');
  const bArtist = document.getElementById('receiptArtist');
  const receiptTotal = document.getElementById('receiptTotalVal');
  const waBtn = document.getElementById('estimatorWaBtn');

  function calculateBudget() {
    if (!guestSlider || !receiptTotal) return;

    const guests = parseInt(guestSlider.value, 10);
    if (guestDisplay) guestDisplay.textContent = guests + ' Guests';

    const typeCost = parseInt(typeSelect ? typeSelect.value : 500000, 10);
    const venueCost = parseInt(venueSelect ? venueSelect.value : 350000, 10);
    const decorCost = parseInt(decorSelect ? decorSelect.value : 400000, 10);
    const perPlateCost = parseInt(cateringSelect ? cateringSelect.value : 1200, 10);
    const artistCost = parseInt(artistSelect ? artistSelect.value : 250000, 10);

    const totalCatering = guests * perPlateCost;
    const totalVenue = venueCost;
    const totalDecor = decorCost + Math.round(typeCost * 0.4);
    const totalArtists = artistCost;
    const totalEstimate = totalVenue + totalDecor + totalCatering + totalArtists;

    const formatINR = (num) => '₹' + num.toLocaleString('en-IN');

    if (bVenue) bVenue.textContent = formatINR(totalVenue);
    if (bDecor) bDecor.textContent = formatINR(totalDecor);
    if (bCatering) bCatering.textContent = formatINR(totalCatering);
    if (bArtist) bArtist.textContent = formatINR(totalArtists);
    if (receiptTotal) receiptTotal.textContent = formatINR(totalEstimate);

    if (waBtn) {
      const typeText = typeSelect ? typeSelect.options[typeSelect.selectedIndex].text : 'Royal Wedding';
      const venueText = venueSelect ? venueSelect.options[venueSelect.selectedIndex].text : 'Heritage Palace';
      const decorText = decorSelect ? decorSelect.options[decorSelect.selectedIndex].text : 'Royal Mandap';
      const message = `Hello Bhakti Events! I used your online Celebration Estimator and would like to inquire about booking:
*Event Type:* ${typeText}
*Guest Count:* ${guests} Guests
*Venue Preference:* ${venueText}
*Decor Vision:* ${decorText}
*Estimated Package:* ${formatINR(totalEstimate)}
Please let me know how we can proceed with a consultation.`;

      waBtn.href = `https://wa.me/919825661046?text=${encodeURIComponent(message)}`;
    }
  }

  if (guestSlider) {
    guestSlider.addEventListener('input', calculateBudget);
  }
  const guestChips = document.querySelectorAll('.guest-chip');
  guestChips.forEach((chip) => {
    chip.addEventListener('click', () => {
      if (guestSlider) {
        guestSlider.value = chip.dataset.guests;
        calculateBudget();
      }
    });
  });

  [typeSelect, venueSelect, decorSelect, cateringSelect, artistSelect].forEach((sel) => {
    if (sel) sel.addEventListener('change', calculateBudget);
  });
  // Initial run
  calculateBudget();

  // 6. Curated Royal Portfolio Gallery Filter Tabs
  const filterBtns = document.querySelectorAll('.portfolio-filter-bar .filter-btn');
  const portfolioCards = document.querySelectorAll('.portfolio-grid .portfolio-card');

  filterBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      filterBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;

      portfolioCards.forEach((card) => {
        if (filter === 'all' || card.dataset.category === filter) {
          card.classList.remove('hidden');
        } else {
          card.classList.add('hidden');
        }
      });
    });
  });

  // 7. Live Event Countdown Timer (event-details.php)
  const countdownBox = document.getElementById('liveEventCountdown');
  if (countdownBox) {
    const targetDateStr = countdownBox.dataset.targetDate; // Format: YYYY-MM-DD HH:MM:SS
    const targetTime = new Date(targetDateStr).getTime();

    const updateCountdown = () => {
      const now = new Date().getTime();
      const diff = targetTime - now;

      if (diff <= 0) {
        countdownBox.innerHTML = '<div style="font-size:16px; font-weight:700; color:var(--gold-light); padding:10px;"><i class="fa-solid fa-crown me-2"></i> Celebration Is Live / In Progress</div>';
        return;
      }

      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      const dEl = document.getElementById('cdDays');
      const hEl = document.getElementById('cdHours');
      const mEl = document.getElementById('cdMins');
      const sEl = document.getElementById('cdSecs');

      if (dEl) dEl.textContent = String(days).padStart(2, '0');
      if (hEl) hEl.textContent = String(hours).padStart(2, '0');
      if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
      if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
    };

    updateCountdown();
    setInterval(updateCountdown, 1000);
  }

  // 8. Multi-Tier Pass Selection & Quantity Price Calculation
  const tierOptions = document.querySelectorAll('.pass-tier-grid .tier-option');
  const pricePerTicketEl = document.getElementById('pricePerTicket');
  const subtotalDisplay = document.getElementById('summarySubtotal');
  const totalDisplay = document.getElementById('summaryTotal');
  const qtyInput = document.getElementById('ticketQuantity');
  const minusBtn = document.getElementById('qtyMinus');
  const plusBtn = document.getElementById('qtyPlus');
  const availableSeatsEl = document.getElementById('availableSeatsVal');
  const selectedTierInput = document.getElementById('selectedTierInput');

  let currentMultiplier = 1;
  const basePrice = pricePerTicketEl ? parseFloat(pricePerTicketEl.dataset.basePrice || pricePerTicketEl.dataset.price) : 0;

  function updateTicketCalculations() {
    if (!pricePerTicketEl || !subtotalDisplay || !totalDisplay || !qtyInput) return;

    const maxSeats = parseInt(availableSeatsEl ? availableSeatsEl.dataset.max : 10, 10) || 10;
    let qty = parseInt(qtyInput.value, 10) || 1;
    if (qty < 1) qty = 1;
    if (qty > maxSeats) qty = maxSeats;
    qtyInput.value = qty;

    const activeUnitPrice = basePrice * currentMultiplier;
    pricePerTicketEl.textContent = '₹' + activeUnitPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const total = qty * activeUnitPrice;
    const formatted = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    subtotalDisplay.textContent = formatted;
    totalDisplay.textContent = formatted;
  }

  tierOptions.forEach((option) => {
    option.addEventListener('click', () => {
      tierOptions.forEach((opt) => opt.classList.remove('selected'));
      option.classList.add('selected');
      const radio = option.querySelector('.tier-radio');
      if (radio) radio.checked = true;

      currentMultiplier = parseFloat(option.dataset.multiplier) || 1;
      if (selectedTierInput) selectedTierInput.value = option.dataset.tierName || 'Standard Pass';
      updateTicketCalculations();
    });
  });

  if (minusBtn && qtyInput) {
    minusBtn.addEventListener('click', () => {
      let current = parseInt(qtyInput.value, 10) || 1;
      if (current > 1) {
        qtyInput.value = current - 1;
        updateTicketCalculations();
      }
    });
  }

  if (plusBtn && qtyInput) {
    plusBtn.addEventListener('click', () => {
      const maxSeats = parseInt(availableSeatsEl ? availableSeatsEl.dataset.max : 10, 10) || 10;
      let current = parseInt(qtyInput.value, 10) || 1;
      if (current < maxSeats) {
        qtyInput.value = current + 1;
        updateTicketCalculations();
      }
    });
  }

  if (qtyInput) {
    qtyInput.addEventListener('input', updateTicketCalculations);
  }

  // 9. Flash Alerts Auto-Dismissal
  const flashAlerts = document.querySelectorAll('.alert');
  flashAlerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      alert.style.transition = 'all 0.4s ease';
      setTimeout(() => alert.remove(), 400);
    }, 5000);
  });
});

/**
 * Academic Credits Modal Controllers
 */
function openProjectModal() {
  const modal = document.getElementById('projectModal');
  if (modal) modal.style.display = 'flex';
}

function closeProjectModal() {
  const modal = document.getElementById('projectModal');
  if (modal) modal.style.display = 'none';
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeProjectModal();
});

/**
 * Demo Credentials Filler for Testing
 */
function fillCredentials(role) {
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  if (role === 'admin') {
    if (emailInput) emailInput.value = 'admin@eventsphere.com';
    if (passwordInput) passwordInput.value = 'password123';
  } else if (role === 'student' || role === 'guest') {
    if (emailInput) emailInput.value = 'ankita@gmail.com';
    if (passwordInput) passwordInput.value = 'password123';
  }
}
