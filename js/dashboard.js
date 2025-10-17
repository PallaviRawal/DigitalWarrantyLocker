// Chart.js - Warranty Status
const ctx = document.getElementById('warrantyChart');
if (ctx) {
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Active', 'Expiring Soon', 'Expired'],
      datasets: [{
        label: 'Warranties',
        data: [10, 3, 5],
        backgroundColor: [
          'rgba(34, 160, 107, 0.7)',
          'rgba(245, 158, 11, 0.7)',
          'rgba(228, 88, 88, 0.7)'
        ],
        borderColor: [
          'rgba(34, 160, 107, 1)',
          'rgba(245, 158, 11, 1)',
          'rgba(228, 88, 88, 1)'
        ],
        borderWidth: 1,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1b3c53',
          titleColor: '#fff',
          bodyColor: '#fff'
        }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
}

// User menu dropdown
const userMenu = document.getElementById('userMenu');
const dropdown = document.getElementById('userDropdown');
if (userMenu) {
  userMenu.addEventListener('click', (e) => {
    e.stopPropagation();
    userMenu.classList.toggle('open');
    const isOpen = userMenu.classList.contains('open');
    userMenu.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  document.addEventListener('click', (e) => {
    if (!userMenu.contains(e.target)) {
      userMenu.classList.remove('open');
      userMenu.setAttribute('aria-expanded', 'false');
    }
  });
}

// Load notifications once on page load
document.addEventListener('DOMContentLoaded', function () {
  fetch('get_notifications.php')
    .then(res => res.json())
    .then(data => {
      if (data.error) return;

      // Badge count
      const countElem = document.getElementById('notification-count');
      if (countElem) countElem.textContent = data.unread_count;

      // Build list
      const notifList = document.getElementById('notif-list');
      if (notifList) {
        if (data.notifications.length > 0) {
          notifList.innerHTML = data.notifications.map(notif => `
            <div class="notif-item ${notif.is_read == 0 ? 'unread' : ''}">
              <a href="${notif.link}">
                <strong>${notif.title}</strong><br>
                <span>${notif.message}</span><br>
                <small>${notif.created_at}</small>
              </a>
            </div>
          `).join('');
        } else {
          notifList.innerHTML = '<div class="notif-empty">No notifications</div>';
        }
      }
    })
    .catch(err => console.error('Notification load failed:', err));
});
