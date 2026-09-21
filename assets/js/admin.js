/**
 * Bhakti Events & Celebrations — Admin Console Interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Real-time Digital Clock in Admin Topbar
  const clockEl = document.getElementById('adminClock');
  if (clockEl) {
    const updateClock = () => {
      const now = new Date();
      clockEl.textContent = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      });
    };
    updateClock();
    setInterval(updateClock, 1000);
  }

  // 2. Live Table Search Filter
  const searchInput = document.getElementById('tableSearch');
  const table = document.querySelector('.admin-table tbody');

  if (searchInput && table) {
    searchInput.addEventListener('input', () => {
      const filter = searchInput.value.toLowerCase().trim();
      const rows = table.querySelectorAll('tr');

      rows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });
  }

  // 3. Status Filter Dropdown
  const statusFilter = document.getElementById('statusFilter');
  if (statusFilter && table) {
    statusFilter.addEventListener('change', () => {
      const selected = statusFilter.value.toLowerCase();
      const rows = table.querySelectorAll('tr');

      rows.forEach((row) => {
        const badge = row.querySelector('.status-badge');
        if (!selected || !badge) {
          row.style.display = '';
          return;
        }
        const badgeText = badge.innerText.toLowerCase();
        row.style.display = badgeText.includes(selected) ? '' : 'none';
      });
    });
  }

  // 4. Image Upload Preview
  const imageInput = document.getElementById('eventImage');
  const previewImg = document.getElementById('previewImg');
  const previewPlaceholder = document.getElementById('previewPlaceholder');

  if (imageInput && previewImg) {
    imageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.display = 'block';
          if (previewPlaceholder) {
            previewPlaceholder.style.display = 'none';
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

function confirmAction(message) {
  return confirm(message || 'Are you sure you want to proceed with this action?');
}
